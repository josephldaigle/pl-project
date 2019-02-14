<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 4:04 PM
 */

namespace PapaLocal\ReferralAgreement\Event;


use PapaLocal\Billing\Event\UserBalanceFellBelowThreshold;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Class UserBalanceSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Event
 */
class UserBalanceSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferralAgreementRepository
     */
    private $agreementRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UserBalanceSubscriber constructor.
     *
     * @param ReferralAgreementRepository $agreementRepository
     * @param RequestStack                $requestStack
     * @param LoggerInterface             $logger
     */
    public function __construct(ReferralAgreementRepository $agreementRepository,
                                RequestStack $requestStack,
                                LoggerInterface $logger)
    {
        $this->agreementRepository = $agreementRepository;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }


    /**
     * @param UserBalanceFellBelowThreshold $event
     */
    public function pauseAgreements(UserBalanceFellBelowThreshold $event)
    {
        $userGuid = $event->getAccountOwnerUserGuid();
        $author = $this->requestStack->getCurrentRequest()->attributes->get('_sysadmin');

        try {
            // set all user agreements to 'INACTIVE'
            $agreements = $this->agreementRepository->loadUserAgreements($userGuid);

            foreach ($agreements as $agreement) {
                $this->agreementRepository->updateStatus($agreement->getGuid(), Status::INACTIVE(), StatusChangeReason::INSUFFICIENT_FUNDS(), $author->getGuid());
            }

            return;

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An exception occurred while pausing agreements: %s', $exception->getMessage()), array($exception));

        }

        return;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserBalanceFellBelowThreshold::class => 'pauseAgreements'
        ];
    }

}