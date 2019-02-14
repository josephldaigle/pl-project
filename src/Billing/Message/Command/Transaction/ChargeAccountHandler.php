<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 10:29 AM
 */

namespace PapaLocal\Billing\Message\Command\Transaction;


use PapaLocal\Billing\Data\MessageFactory;
use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Billing\Notification\NotificationFactory;
use PapaLocal\Billing\ValueObject\TransactionTier;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Notification\Notifier;
use PapaLocal\Referral\Message\MessageFactory as RMessageFactory;
use PapaLocal\ReferralAgreement\Message\MessageFactory as AMessageFactory;
use PapaLocal\IdentityAccess\Message\MessageFactory as IMessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ChargeAccountHandler
 * @package PapaLocal\Billing\Message\Command\Transaction
 */
class ChargeAccountHandler
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
     * @var AMessageFactory
     */
    private $aMessageFactory;

    /**
     * @var RMessageFactory
     */
    private $rMessageFactory;

    /**
     * @var IMessageFactory
     */
    private $iMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

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
     * ChargeAccountHandler constructor.
     * @param GuidGeneratorInterface $guidFactory
     * @param TransactionRepository $transactionRepository
     * @param MessageFactory $messageFactory
     * @param AMessageFactory $aMessageFactory
     * @param RMessageFactory $rMessageFactory
     * @param IMessageFactory $iMessageFactory
     * @param MessageBusInterface $appBus
     * @param MessageBusInterface $mysqlBus
     * @param SerializerInterface $serializer
     * @param NotificationFactory $notificationFactory
     * @param Notifier $notifier
     */
    public function __construct(GuidGeneratorInterface $guidFactory, TransactionRepository $transactionRepository, MessageFactory $messageFactory, AMessageFactory $aMessageFactory, RMessageFactory $rMessageFactory, IMessageFactory $iMessageFactory, MessageBusInterface $appBus, MessageBusInterface $mysqlBus, SerializerInterface $serializer, NotificationFactory $notificationFactory, Notifier $notifier)
    {
        $this->guidFactory = $guidFactory;
        $this->transactionRepository = $transactionRepository;
        $this->messageFactory = $messageFactory;
        $this->aMessageFactory = $aMessageFactory;
        $this->rMessageFactory = $rMessageFactory;
        $this->iMessageFactory = $iMessageFactory;
        $this->appBus = $appBus;
        $this->mysqlBus = $mysqlBus;
        $this->serializer = $serializer;
        $this->notificationFactory = $notificationFactory;
        $this->notifier = $notifier;
    }

    /**
     * @inheritDoc
     */
    function __invoke(ChargeAccount $command)
    {
        // Generate transaction guid
        $agreementOwnerTransactionGuid = $this->guidFactory->generate();

        // Retrieve agreement owner guid
        $agreementQuery = $this->aMessageFactory->newFindAgreementByGuid($command->getAgreementGuid());
        $agreement = $this->appBus->dispatch($agreementQuery);

        // Retrieve agreement owner billing profile
        $aQuery = $this->messageFactory->newFindByUserGuid($agreement->getOwnerGuid()->value());
        $agreementOwnerBillingProfile = $this->mysqlBus->dispatch($aQuery);

        // Create agreement owner transaction (Debit)
        $agreementOwnerTransaction = $this->serializer->denormalize(array(
            'userId' => $agreementOwnerBillingProfile->getUserId(),
            'billingProfileId' => $agreementOwnerBillingProfile->getId(),
            'amount' => $agreement->getBid(),
            'description' => Transaction::DESC_REFERRAL,
            'aNetTransId' => '',
            'type' => Transaction::TYPE_DEBIT
        ), Transaction::class, 'array');

        // Save transaction
        $this->transactionRepository->saveSuccessfulTransaction($agreementOwnerTransaction, $agreementOwnerTransactionGuid);

        try {
            // Generate transaction guid
            $referrerTransactionGuid = $this->guidFactory->generate();

            // Retrieve referrer guid
            $referralQuery = $this->rMessageFactory->newFindByGuid($command->getReferralGuid());
            $referral = $this->appBus->dispatch($referralQuery);

            // Retrieve referrer billing profile
            $rQuery = $this->messageFactory->newFindByUserGuid($referral->getProviderUserGuid()->value());
            $referrerBillingProfile = $this->mysqlBus->dispatch($rQuery);

            $referrerCut = floor($agreement->getBid() * TransactionTier::TIER_ONE_USER);

            // Create referrer transaction (Credit 60% of agreement bid)
            $referrerTransaction = $this->serializer->denormalize(array(
                'userId' => $referrerBillingProfile->getUserId(),
                'billingProfileId' => $referrerBillingProfile->getId(),
                'amount' => $referrerCut,
                'description' => Transaction::DESC_REFERRAL,
                'aNetTransId' => '',
                'type' => Transaction::TYPE_CREDIT
            ), Transaction::class, 'array');

            // Save transaction
            $this->transactionRepository->saveSuccessfulTransaction($referrerTransaction, $referrerTransactionGuid);

        } catch (\Exception $e) {
            $this->transactionRepository->removeFailedTransaction($agreementOwnerTransactionGuid);
            throw $e;
        }

        // Get agreement owner username
        $agreementOwnerQuery = $this->iMessageFactory->newFindUserByGuid($agreement->getOwnerGuid());
        $agreementOwner = $this->appBus->dispatch($agreementOwnerQuery);

        // Send notifications to agreement owner
        $agreementOwnerNotification = $this->notificationFactory->newChargeSuccess(
            $agreement->getBid(),
            $agreementOwner->getUsername(),
            array()
        );
        $this->notifier->sendUserNotification($agreement->getOwnerGuid(), $agreementOwnerNotification);

        // Get referrer username
        $referrerQuery = $this->iMessageFactory->newFindUserByGuid($agreement->getOwnerGuid());
        $referrer = $this->appBus->dispatch($referrerQuery);

        // Send notifications to referrer
        $referrerNotification = $this->notificationFactory->newChargeSuccess(
            $referrerCut,
            $referrer->getUsername(),
            array()
        );
        $this->notifier->sendUserNotification($referral->getProviderUserGuid(), $referrerNotification);
    }
}