<?php
/**
 * Created by eWebify, LLC.
 * Creator: Joe Daigle <joe@ewebify.com>
 * Date: 9/24/18
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class CreateTransitionSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class CreateTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * CreateTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $messageFactory
     */
    public function __construct(MessageBusInterface $mysqlBus, MessageFactory $messageFactory)
    {
        $this->mysqlBus       = $mysqlBus;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function createAgreement(Event $event)
    {
        // fetch the event
        $referralAgreement = $event->getSubject();

        // begin transaction
        try {
            $this->mysqlBus->dispatch($this->messageFactory->newStartTransaction());

            // save agreement
            $createAgmtCmd = $this->messageFactory->newSaveAgreement($referralAgreement);
            $this->mysqlBus->dispatch($createAgmtCmd);

            // combine locations into one list
            $locations = $referralAgreement->getIncludedLocations();
            $locations->addAll($referralAgreement->getExcludedLocations());

            $updateLocationsCmd = $this->messageFactory->newUpdateLocations($referralAgreement->getGuid(),
                $locations);
            $this->mysqlBus->dispatch($updateLocationsCmd);

            // save services
            $services = $referralAgreement->getIncludedServices();
            $services->addAll($referralAgreement->getExcludedServices());

            $updateServicesCmd = $this->messageFactory->newUpdateServices($referralAgreement->getGuid(),
                $services);
            $this->mysqlBus->dispatch($updateServicesCmd);

            // save new status
            $createStatusCmd = $this->messageFactory->newUpdateAgreementStatus($referralAgreement->getStatusHistory()->getCurrentStatus());
            $this->mysqlBus->dispatch($createStatusCmd);

            // commit transaction
            $this->mysqlBus->dispatch($this->messageFactory->newCommitTransaction());

        } catch (\Exception $exception) {
            // rollback transaction
            $this->mysqlBus->dispatch($this->messageFactory->newRollbackTransaction());

            throw $exception;
        }
        
        return;
    }

    /**
     * @return array
     * {@inheritdoc}
     *
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_agreement.transition.create' => 'createAgreement'
        ];
    }
}