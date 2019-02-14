<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/16/19
 * Time: 2:38 PM
 */

namespace PapaLocal\Billing\Message\Command\Transaction;


use PapaLocal\Billing\Data\MessageFactory;
use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Billing\Exception\ExcessiveWithdrawalAmountException;
use PapaLocal\Billing\Exception\ExcessiveWithdrawalAttemptException;
use PapaLocal\Billing\Notification\NotificationFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Entity\Billing\TransactionList;
use PapaLocal\Feed\Enum\FeedItemType;
use PapaLocal\Notification\Notifier;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class PayoutHandler
 * @package PapaLocal\Billing\Message\Command\Transaction
 */
class PayoutHandler
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * PayoutHandler constructor.
     * @param GuidGeneratorInterface $guidFactory
     * @param TransactionRepository $transactionRepository
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $mysqlBus
     * @param SerializerInterface $serializer
     * @param NotificationFactory $notificationFactory
     * @param Notifier $notifier
     */
    public function __construct(GuidGeneratorInterface $guidFactory,
                                TransactionRepository $transactionRepository,
                                MessageFactory $messageFactory,
                                MessageBusInterface $mysqlBus,
                                SerializerInterface $serializer,
                                NotificationFactory $notificationFactory,
                                Notifier $notifier)
    {
        $this->guidFactory = $guidFactory;
        $this->transactionRepository = $transactionRepository;
        $this->messageFactory = $messageFactory;
        $this->mysqlBus = $mysqlBus;
        $this->serializer = $serializer;
        $this->notificationFactory = $notificationFactory;
        $this->notifier = $notifier;
    }

    /**
     * @inheritDoc
     */
    function __invoke(Payout $command)
    {
        // Get user billing profile
        $query = $this->messageFactory->newFindByUserGuid($command->getForm()->getUserGuid());
        $billingProfile = $this->mysqlBus->dispatch($query);

        // Get previous payout info
        $transactionList = $this->transactionRepository->loadUsersTransactions($billingProfile->getUserId());
        $mostRecentPayout = $transactionList->getAllWithdrawals()->sortByDate(TransactionList::SORT_ORDER_DESC)->first();

        $today = new \DateTime(date('Y-m-d H:i:s', time()));
        $lastPayoutDate = new \DateTime($mostRecentPayout->getTimeCreated());

        $interval = new \DateInterval('P30D');
        $today->sub($interval);

        if ($lastPayoutDate >= $today) {
            // Add message about exception
            throw new ExcessiveWithdrawalAttemptException();
        }

        if ($billingProfile->getAvailableBalance() < $command->getForm()->getAmount()) {
            // Add message about exception
            throw new ExcessiveWithdrawalAmountException();
        }

        // Generate guid
        $transactionGuid = $this->guidFactory->generate();

        // Create transaction
        $transaction = $this->serializer->denormalize(array(
            'userId' => $billingProfile->getUserId(),
            'billingProfileId' => $billingProfile->getId(),
            'amount' => $command->getForm()->getAmount(),
            'description' => Transaction::DESC_WITHDRAW,
            'aNetTransId' => '',
            'type' => Transaction::TYPE_DEBIT
        ), Transaction::class, 'array');

        // Save payout
        $this->transactionRepository->saveSuccessfulTransaction($transaction, $transactionGuid);

        /**
         * This is where a notification is sent out to the user and the admin, after successful payout.
         */
        try {
            // Send notification to user
            $recipientNotification = $this->notificationFactory->newPayoutSuccess(
                $command->getForm()->getAmount(),
                $billingProfile->getAvailableBalance(),
                $command->getUsername(),
                array()
            );
            $this->notifier->sendUserNotification($command->getForm()->getUserGuid(), $recipientNotification);

            // Send notification to admin
            $sysAdminNotification = $this->notificationFactory->newPayoutSuccess(
                $command->getForm()->getAmount(),
                $billingProfile->getAvailableBalance(),
                $command->getUsername(),
                array()
            );
            $sysAdminNotification->setAssociateFeedItem(
                $this->serializer->denormalize(
                    array(
                        'guid' => array('value' => $transactionGuid),
                        'type' => array('value' => FeedItemType::TRANSACTION()->getValue())
                    ), AssociateFeedItem::class, 'array'
                )
            );

            $this->notifier->sendUserNotification(
            // sysadmin Guid.
                $this->serializer->denormalize(
                    array('value' => '20671da2-82c6-4b30-8140-b7146cc8033b'),
                    Guid::class, 'array'
                ),
                $sysAdminNotification
            );
        } catch (\Exception $e) {
            // Remove transaction if notifications don't go out.
            $this->transactionRepository->removeFailedTransaction($transactionGuid);
            throw $e;
        }
    }
}