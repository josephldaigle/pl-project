<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/18
 * Time: 8:45 PM
 */


namespace PapaLocal\IdentityAccess\Service;


use PapaLocal\Core\Service\ServiceInterface;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Entity\UserAccount;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Workflow\Registry;


/**
 * Class UserService
 *
 * @package PapaLocal\IdentityAccess\Service
 */
class UserService implements ServiceInterface
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserService constructor.
     *
     * @param Registry                     $workflowRegistry
     * @param UserRepository               $userRepository
     * @param MessageFactory               $mysqlMsgFactory
     * @param MessageBusInterface          $mysqlBus
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        Registry $workflowRegistry,
        UserRepository $userRepository,
        MessageFactory $mysqlMsgFactory,
        MessageBusInterface $mysqlBus,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->userRepository   = $userRepository;
        $this->mysqlMsgFactory  = $mysqlMsgFactory;
        $this->mysqlBus         = $mysqlBus;
        $this->passwordEncoder  = $passwordEncoder;
    }


    /**
     * @param UserAccount $userAccount
     */
    public function createUserAccount(UserAccount $userAccount)
    {
        // encode password
        $password = $this->passwordEncoder->encodePassword(new User(), $userAccount->getUser()->getPassword());
        $user = $userAccount->getUser()->setPassword($password);
        $userAccount->setUser($user);

        // create user account
        $workflow = $this->workflowRegistry->get($userAccount, 'user_account');
        $workflow->apply($userAccount, 'create');

        return;
    }

    /**
     * Update a user's password.
     *
     * @param GuidInterface $userGuid
     * @param string        $password
     *
     * @throws \PapaLocal\Entity\Exception\ServiceOperationFailedException
     */
    public function updatePassword(GuidInterface $userGuid, string $password)
    {
        // encode password
        // override $password to prevent accidentally storing unencoded
        $password = $this->passwordEncoder->encodePassword(new User(), $password);

        // update password
        $updatePassCmd = $this->mysqlMsgFactory->newUpdatePassword($userGuid, $password);
        $this->mysqlBus->dispatch($updatePassCmd);

        return;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findByGuid(GuidInterface $userGuid)
    {
        return $this->userRepository->findUserByGuid($userGuid);
    }

    /**
     * @param EmailAddress $username
     *
     * @return User
     * @throws \PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @throws \PapaLocal\Entity\Exception\UserNotFoundException
     */
    public function findByUsername(EmailAddress $username): User
    {
        return $this->userRepository->loadUser($username->getEmailAddress());
    }
}