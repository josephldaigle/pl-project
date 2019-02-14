<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/28/18
 * Time: 2:33 PM
 */


namespace PapaLocal\IdentityAccess\Data;


use PapaLocal\Core\ValueObject\EmailAddress as EmailVO;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Data\Command\Person\CreatePersonAddress;
use PapaLocal\Data\Command\Person\CreatePersonPhone;
use PapaLocal\Data\Command\Person\UpdatePersonAddress;
use PapaLocal\Data\Command\Person\UpdatePersonEmail;
use PapaLocal\Data\Command\Person\UpdatePersonPhone;
use PapaLocal\Data\Command\User\CreateUser;
use PapaLocal\Data\Command\User\FindByUsername;
use PapaLocal\Data\Command\User\LoadUserRoles;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Exception\UsernameExistsException;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Entity\User;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\IdentityAccess\Entity\Factory\UserFactory;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UserRepository
 *
 * @package PapaLocal\IdentityAccess\Data
 */
class UserRepository extends AbstractRepository
{
    /**
     * @var UserContactDetailHydrator
     */
    private $profileHydrator;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * UserRepository constructor.
     *
     * @param DataResourcePool                                         $dataResourcePool
     * @param \PapaLocal\IdentityAccess\Data\UserContactDetailHydrator $userContactDetailHydrator
     * @param MessageBusInterface                                      $mysqlBus
     * @param MessageFactory                                           $mysqlMessageFactory
     * @param UserFactory                                              $userFactory
     */
    public function __construct(DataResourcePool $dataResourcePool,
                                UserContactDetailHydrator $userContactDetailHydrator,
                                MessageBusInterface $mysqlBus,
                                MessageFactory $mysqlMessageFactory,
                                UserFactory $userFactory
    )
    {
        parent::__construct($dataResourcePool);

        $this->profileHydrator = $userContactDetailHydrator;
        $this->mysqlBus = $mysqlBus;
        $this->mysqlMsgFactory = $mysqlMessageFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserByGuid(GuidInterface $userGuid)
    {
        // fetch a command from factory
        $query = $this->mysqlMsgFactory->newFindBy('v_user', 'userGuid', $userGuid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        if ($recordSet->count() < 1) {
            throw new UserNotFoundException(sprintf('Unable to find a user with guid: %s', $userGuid->value()));
        }

        $user = $this->userFactory->createFromRecord($recordSet->current());
        $user->setRoles($this->loadUserRoles($recordSet->current()['userId']));

        return $user;
    }

    /**
     * @param EmailVO $username
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserByUsername(EmailVO $username)
    {
        $findByQry = $this->mysqlMsgFactory->newFindBy('v_user', 'username', $username->getEmailAddress());
        $recordSet = $this->mysqlBus->dispatch($findByQry);

        if ($recordSet->count() < 1) {
            throw new UserNotFoundException(sprintf('Unable to find a user with username: %s', $username->getEmailAddress()));
        }
        
        $user = $this->userFactory->createFromRecord($recordSet->current());
        $user->setRoles($this->loadUserRoles($recordSet->current()['userId']));

        return $user;
    }

    /**
     * @deprecated
     * Creates a new user account.
     *
     *
     * @param User   $user
     * @param Person $person
     * @return User|mixed
     * @throws ServiceOperationFailedException
     * @throws UsernameExistsException
     * @throws \ReflectionException
     */
    public function createUserAccount(User $user, Person $person)
    {
        // check if user exists for username
        //TODO: Factor into transition guard
        if ($this->userExists($user->getUsername())) {
            throw new UsernameExistsException(sprintf('An account exists for username %s.', $user->getUsername()));
        }

        $user->setPerson($person);
        $createUserCommand = $this->commandFactory->createCommand(CreateUser::class, array($user));
        $user = $createUserCommand->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

        return $user;
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $newUsername
     *
     * @return mixed
     * @throws ServiceOperationFailedException
     * @throws UsernameExistsException
     */
    public function updateUsername(GuidInterface $userGuid, string $newUsername)
    {
        // check if username exists
        $this->tableGateway->setTable('v_user');
        $usernameRows = $this->tableGateway->findBy('username', $newUsername);

        if (count($usernameRows) > 0) {
            throw new UsernameExistsException('The username supplied is already in use.');
        }
        // update the username
        $updateUsernameCmd = $this->mysqlMsgFactory->newUpdateUsername($userGuid, $newUsername);
        $this->mysqlBus->dispatch($updateUsernameCmd);

        return;
    }

    /**
     * Update a user's firstName.
     *
     * @param User   $user
     * @param string $firstName
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function updateFirstName(User $user, string $firstName)
    {
        try {
            // update users password
            $this->tableGateway->setTable('Person');
            $userRow = $this->tableGateway->findById($user->getPerson()->getGuid());

            $userRow[0]['firstName'] = $firstName;

            return $this->tableGateway->update($userRow[0]);

        } catch (\Exception $exception) {
            throw new ServiceOperationFailedException(sprintf('Failed updating first name for user %s: %s',
                $user->getUsername(), $exception->getMessage()));
        }
    }

    /**
     * Update a user's lastName.
     *
     * @param User   $user
     * @param string $lastName
     *
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function updateLastName(User $user, string $lastName)
    {
        try {
            // update users password
            $this->tableGateway->setTable('Person');
            $userRow = $this->tableGateway->findById($user->getPerson()->getGuid());

            $userRow[0]['lastName'] = $lastName;

            return $this->tableGateway->update($userRow[0]);

        } catch (\Exception $exception) {
            throw new ServiceOperationFailedException(sprintf('Failed updating password for user %s: %s',
                $user->getUsername(), $exception->getMessage()));
        }
    }

    /**
     * Update a user's email address.
     * 
     * @param User         $user
     * @param EmailAddress $emailAddress
     *
     * @return mixed
     * @throws ServiceOperationFailedException
     */
    public function updateEmailAddress(User $user, EmailAddress $emailAddress)
    {
        try {
            $updateEmailCmd = $this->commandFactory->createCommand(UpdatePersonEmail::class, array(
                $user->getPerson()->getGuid()->value(), $emailAddress
            ));

            return $this->$updateEmailCmd($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

        } catch (\Exception $exception) {
            throw new ServiceOperationFailedException(sprintf('Failed updating %s email address from %s to %s: %s',
                $emailAddress->getType(), $user->getUsername(), $emailAddress->getEmailAddress(), $exception->getMessage()));
        }
    }

    /**
     * Save a user's phone number.
     *
     * @param User        $user
     * @param PhoneNumber $phoneNumber
     *
     * @return mixed
     * @throws ServiceOperationFailedException
     */
    public function savePhoneNumber(User $user, PhoneNumber $phoneNumber)
    {

        try {
            $this->profileHydrator->setEntity($user);
            $this->profileHydrator->hydrate();

            if ($user->getContactProfile()->getPhoneNumberList()->findBy('type', $phoneNumber->getType())) {
                // update the user's phone type
	            $updatePersonPhoneCmd = $this->commandFactory->createCommand(UpdatePersonPhone::class, array(
		            'personId' => $user->getPerson()->getGuid(),
		            'phoneNumber' => $phoneNumber
	            ));

                return $updatePersonPhoneCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            } else {
            	$createPersonPhoneCmd = $this->commandFactory->createCommand(CreatePersonPhone::class, array(
		            'personId' => $user->getPerson()->getGuid(),
		            'phoneNumber' => $phoneNumber
	            ));
                return $createPersonPhoneCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);
            }

        } catch (\Exception $exception) {
            throw new ServiceOperationFailedException(sprintf('Failed updating phoneNumber for %s [%s]: %s',
                $user->getUsername(), $phoneNumber->getPhoneNumber(), $exception->getMessage()));
        }
    }

    /**
     * Save a user's address.
     *
     * @param User    $user
     * @param Address $address
     *
     * @return mixed
     * @throws ServiceOperationFailedException
     */
    public function saveAddress(User $user, Address $address)
    {
        try {

            $this->profileHydrator->setEntity($user);
            $this->profileHydrator->hydrate();

            if ($currAddr = $user->getContactProfile()->getAddressList()->findBy('type', $address->getType())) {
                // user has address with given type
                if ($address->isEqualTo($currAddr)) {
                    return;
                }

                // user has type, but address is different
                // update address for user
	            $updatePersonAddressCmd = $this->commandFactory->createCommand(UpdatePersonAddress::class, array(
		            'personId' => $user->getPerson()->getGuid(),
		            'address' => $address
	            ));
                return $updatePersonAddressCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            } else {
                // user does not have address with given type
                // create address for user
	            $createPersonAddressCmd = $this->commandFactory->createCommand(CreatePersonAddress::class, array(
		            'personId' => $user->getPerson()->getGuid(),
		            'address' => $address
	            ));
                return $this->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);
            }

        } catch (\Exception $exception) {
            throw new ServiceOperationFailedException(sprintf('Failed updating %s address for %s to %s: %s',
                $address->getType(), $user->getUsername(), $address->toString(), $exception->getMessage()));
        }
    }

    /**
     * TODO: Refactor into data command
     * Save's a role to the user's profile.
     *
     * @param int    $userId
     * @param string $type
     *
     * @return bool
     * @throws ServiceOperationFailedException
     */
    public function saveUserRole(int $userId, string $type)
    {
        $roles = $this->loadUserRoles($userId);

        if ($id = array_search($type, $roles)) {
            return $id;
        }

        //save user role
        $this->tableGateway->setTable('L_UserRole');
        $roleRows = $this->tableGateway->findBy('name', $type);

        if (count($roleRows) < 1) {
            throw new ServiceOperationFailedException(sprintf("Unable to find a role matching %s in table L_RoleType.", $type));
        }

        $this->tableGateway->setTable('R_UserApplicationRole');
        $rowId = $this->tableGateway->create(array(
            'userId' => $userId,
            'roleId' => $roleRows[0]['id']
        ));

        return $rowId;
    }

    /**
     * Save the current amount of notification when the notification icon is clicked
     *
     * @param int $userId
     * @param int $savePoint
     * @return int
     */
    public function createSavePoint(int $userId, int $savePoint)
    {
        $this->tableGateway->setTable('User');
        $userRow = $this->tableGateway->findById($userId);

        $userRow[0]['notificationSavePoint'] = $savePoint;

        return $this->tableGateway->update($userRow[0]);
    }

    /**
     * Check if a given username is associated with an active user account.
     *
     * @param string $username
     * @return bool
     * @throws \ReflectionException
     */
    public function userExists(string $username): bool
    {
        // find user
        $findCmd = $this->commandFactory->createCommand(FindByUsername::class, array($username));

        try {
            $user = $findCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            return $user->getIsActive();

        } catch (UserNotFoundException $userNotFoundException) {
            return false;
        }
    }

    /**
     * @deprecated - use findUserByUsername
     * Fetch a user object from storage.
     *
     * @param string $username
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function loadUser(string $username): User
    {
        // find by username
        $findUserQry = $this->mysqlMsgFactory->newFindBy('v_user', 'username', $username);
        $user = $this->mysqlBus->dispatch($findUserQry);

        // load user roles
        $rolesCmd = $this->commandFactory->createCommand(LoadUserRoles::class, array($user->getId()));
        $roles = $rolesCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

        // set roles on user object
        $user->setRoles($roles);

        // load contact profile
        $this->profileHydrator->setEntity($user);
        $user = $this->profileHydrator->hydrate();

        return $user;
    }

    /**
     * Fetch a collection of companies the user owns.
     *
     * @param int $userId
     * @return Collection | false if no companies found
     */
    public function getUserOwnedCompanies(int $userId)
    {
        $companyList = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->tableGateway->setTable('v_company_owner');
        $companyRows = $this->tableGateway->findBy('userId', $userId);

        if (count($companyRows) < 1) {
            return $companyList;
        }

        foreach($companyRows as $row) {
            $company = $this->serializer->denormalize($row, Company::class, 'array');
            $companyList->add($company);
        }

        return $companyList;
    }

	/**
	 * Load a user's roles.
	 *
	 * @param int $userId
	 *
	 * @return array|bool
	 */
	public function loadUserRoles(int $userId)
	{
		// load application roles
        $userRolesQry = $this->mysqlMsgFactory->newFindBy('v_user_roles', 'userId', $userId);
        $userRolesRecSet = $this->mysqlBus->dispatch($userRolesQry);

        $roles = [];
        if ($userRolesRecSet->count() > 0) {
            foreach($userRolesRecSet as $record) {
                $roles[] = $record['role'];
            }
        }

        // load company roles
        $companyRolesQry = $this->mysqlMsgFactory->newFindBy('v_company_owner', 'userId', $userId);
        $companyRolesRecSet = $this->mysqlBus->dispatch($companyRolesQry);

        if ($companyRolesRecSet->count() > 0) {
            $roles[] = SecurityRole::ROLE_COMPANY()->getValue();
        }

        return $roles;
    }
}