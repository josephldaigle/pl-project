<?php
/**
 * Created by Joseph Daigle.
 * Date: 2/3/19
 * Time: 7:36 PM
 */


namespace PapaLocal\ReferralAgreement\Event\Subscriber;


use PapaLocal\IdentityAccess\ValueObject\SystemAdmin;
use PapaLocal\Referral\Event\ReferralCreatedEvent;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * ReferralCreatedSubscriber.
 *
 * @package PapaLocal\ReferralAgreement\Event\Subscriber
 */
class ReferralCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferralAgreementRepository
     */
    private $agreementRepository;

    /**
     * @var ReferralAgreementService
     */
    private $agreementService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ReferralCreatedSubscriber constructor.
     *
     * @param ReferralAgreementRepository $agreementRepository
     * @param ReferralAgreementService    $agreementService
     * @param SerializerInterface         $serializer
     * @param LoggerInterface             $logger
     */
    public function __construct(
        ReferralAgreementRepository $agreementRepository,
        ReferralAgreementService $agreementService,
        SerializerInterface $serializer,
        LoggerInterface $logger
    )
    {
        $this->agreementRepository = $agreementRepository;
        $this->agreementService    = $agreementService;
        $this->serializer          = $serializer;
        $this->logger              = $logger;
    }

    /**
     * @param ReferralCreatedEvent $event
     */
    public function referralCreated(ReferralCreatedEvent $event)
    {
        try {
            $agreement = $this->agreementRepository->findByGuid($event->getAgreementGuid());
            $currCount = $this->agreementRepository->getCurrentPeriodReferralCount($event->getAgreementGuid());

            if ($currCount >= $agreement->getQuantity()) {

                // pause the agreement as fulfilled for the current period
                $agreementStatus = $this->serializer->denormalize(array(
                    'agreementId' => array('value' => $event->getAgreementGuid()->value()),
                    'status'      => array('value' => Status::INACTIVE()->getValue()),
                    'reason'      => array('value' => StatusChangeReason::REFERRAL_QUOTA_REACHED()->getValue()),
                    'updater'     => array('value' => SystemAdmin::GUID),
                    'timeUpdated' => date('Y-m-d H:i:s', time())
                ), AgreementStatus::class, 'array');

                $this->agreementService->pauseAgreement($event->getAgreementGuid(), $agreementStatus);
            }

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), array('exception' => $exception));
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ReferralCreatedEvent::class => 'referralCreated'
        ];
    }

}