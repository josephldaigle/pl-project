<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/5/18
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class PublishTransitionSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class PublishTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * PublishTransitionSubscriber constructor.
     *
     * @param MessageFactory        $mysqlMsgFactory
     * @param MessageBusInterface   $mysqlBus
     * @param TokenStorageInterface $tokenStorage
     * @param SerializerInterface   $serializer
     */
    public function __construct(
        MessageFactory $mysqlMsgFactory,
        MessageBusInterface $mysqlBus,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    )
    {
        $this->mysqlMsgFactory = $mysqlMsgFactory;
        $this->mysqlBus        = $mysqlBus;
        $this->tokenStorage    = $tokenStorage;
        $this->serializer      = $serializer;
    }

    /**
     * @param Event $event
     */
    public function publishAgreement(Event $event)
    {
        $agreement = $event->getSubject();

        $agreementStatus = $this->serializer->denormalize(array(
            'agreementId' => array('value' => $agreement->getGuid()->value()),
            'status' => array('value' => Status::ACTIVE()->getValue()),
            'reason' => array('value' => StatusChangeReason::PUBLISHED()->getValue()),
            'updater' => array('value' => $this->tokenStorage->getToken()->getUser()->getGuid()->value())
        ), AgreementStatus::class, 'array');

        // update agreement status
        $updateAgmtStatusCmd = $this->mysqlMsgFactory->newUpdateAgreementStatus($agreementStatus);
        $this->mysqlBus->dispatch($updateAgmtStatusCmd);

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
            'workflow.referral_agreement.transition.publish' => 'publishAgreement'
        ];
    }

}