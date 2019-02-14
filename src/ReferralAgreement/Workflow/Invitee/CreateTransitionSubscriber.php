<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/3/18
 * Time: 6:59 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\ReferralAgreement\Data\MessageFactory as RA_DataMessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class CreateTransitionSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class CreateTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var RA_DataMessageFactory
     */
    private $raDataMsgFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * CreateTransitionSubscriber constructor.
     *
     * @param MessageFactory        $iaMessageFactory
     * @param MessageBusInterface   $appBus
     * @param RA_DataMessageFactory $raDataMsgFactory
     * @param MessageBusInterface   $mysqlBus
     */
    public function __construct(MessageFactory $iaMessageFactory, MessageBusInterface $appBus, RA_DataMessageFactory $raDataMsgFactory, MessageBusInterface $mysqlBus)
    {
        $this->iaMessageFactory = $iaMessageFactory;
        $this->appBus = $appBus;
        $this->raDataMsgFactory = $raDataMsgFactory;
        $this->mysqlBus = $mysqlBus;
    }

    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function createInvitee(Event $event)
    {
        $invitee = $event->getSubject();

        // check if invitee is a user
        try {
            $findUserQry = $this->iaMessageFactory->newFindUserByUsername($invitee->getEmailAddress()->getEmailAddress());
            $user = $this->appBus->dispatch($findUserQry);

            // set userId if invitee is user
            $invitee->setUserId($user->getGuid());

        } catch (\Exception $exception) {
            // do nothing - could not determine if invitee is user
            // normally, this will be due to user not found exception, however, any exception
            // should prevent the workflow from treating the invitee as a user
        }

        // save invitee
        $saveCmd = $this->raDataMsgFactory->newSaveInvitee($invitee);
        $this->mysqlBus->dispatch($saveCmd);

        return;

    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.transition.create' => 'createInvitee'
        ];
    }

}