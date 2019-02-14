<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 2:10 PM
 */

namespace PapaLocal\IdentityAccess\Workflow\User;


use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class CreateTransitionSubscriber
 *
 * @package PapaLocal\IdentityAccess\Workflow\User
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
    private $mysqlMsgFactory;

    /**
     * CreateTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $mysqlMsgFactory
     */
    public function __construct(MessageBusInterface $mysqlBus, MessageFactory $mysqlMsgFactory)
    {
        $this->mysqlBus        = $mysqlBus;
        $this->mysqlMsgFactory = $mysqlMsgFactory;
    }

    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function transitionToActive(Event $event)
    {
        $userAccount = $event->getSubject();

        try {
            // start mysql transaction
            $this->mysqlBus->dispatch($this->mysqlMsgFactory->newStartTransaction());

            // save agreement using data bus
            $saveUserCommand = $this->mysqlMsgFactory->newCreateUser($userAccount->getUser());
            $this->mysqlBus->dispatch($saveUserCommand);

            // save user roles
            $updateRolesCommand = $this->mysqlMsgFactory->newUpdateUserRoles($userAccount->getUser()->getGuid(), $userAccount->getUser()->getRoles());
            $this->mysqlBus->dispatch($updateRolesCommand);

            $updatePhoneCmd = $this->mysqlMsgFactory->newUpdateUserPhoneNumber($userAccount->getUser()->getGuid(), $userAccount->getPhoneNumber()->getPhoneNumber(), $userAccount->getPhoneNumber()->getType());
            $this->mysqlBus->dispatch($updatePhoneCmd);

            // commit transaction
            $this->mysqlBus->dispatch($this->mysqlMsgFactory->newCommitTransaction());

        } catch (\Exception $exception) {
            // how to handle exception?
            $this->mysqlBus->dispatch($this->mysqlMsgFactory->newRollbackTransaction());
            throw $exception;
        }

        if ($userAccount->hasCompany()) {

            try {
                // start mysql transaction
                $this->mysqlBus->dispatch($this->mysqlMsgFactory->newStartTransaction());

                $company = $userAccount->getCompany();

                // save company
                $saveCompanyCommand = $this->mysqlMsgFactory->newSaveCompany($userAccount->getUser()->getGuid(), $company);
                $this->mysqlBus->dispatch($saveCompanyCommand);

                $saveCoPhoneCmd = $this->mysqlMsgFactory->newUpdateCompanyPhoneNumber($company->getGuid(), $company->getPhoneNumber());
                $this->mysqlBus->dispatch($saveCoPhoneCmd);

                $saveCoEmailCmd = $this->mysqlMsgFactory->newUpdateCompanyEmailAddress($company->getGuid(), $company->getEmailAddress());
                $this->mysqlBus->dispatch($saveCoEmailCmd);

                $saveCoAddrCmd = $this->mysqlMsgFactory->newUpdateCompanyAddress($company->getGuid(), $company->getAddress());
                $this->mysqlBus->dispatch($saveCoAddrCmd);

                // commit transaction
                $this->mysqlBus->dispatch($this->mysqlMsgFactory->newCommitTransaction());

            } catch (\Exception $exception) {
                // how to handle exception?
                $this->mysqlBus->dispatch($this->mysqlMsgFactory->newRollbackTransaction());
                throw $exception;
            }
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.user_account.transition.create' => 'transitionToActive'
        ];
    }

}