<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/15/18
 * Time: 11:26 AM
 */


namespace PapaLocal\IdentityAccess\Service;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Data\AttrType;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Data\Repository\Person\PersonRepository;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\PersonInterface;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\Entity\Exception\UsernameExistsException;
use PapaLocal\Entity\User;
use PapaLocal\ValueObject\Form\RegisterUser;


/**
 * Class UserAccountManager
 *
 * @package PapaLocal\IdentityAccess\Service
 */
class UserAccountManager extends AbstractRepository
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var PersonRepository
	 */
	private $personRepository;


	/**
	 * UserAccountManager constructor.
	 *
	 * @param DataResourcePool $dataResourcePool
	 * @param UserRepository   $userRepository
	 */
	public function __construct(DataResourcePool $dataResourcePool, UserRepository $userRepository, PersonRepository $personRepository)
	{
		parent::__construct($dataResourcePool);
		$this->userRepository = $userRepository;
		$this->personRepository = $personRepository;
	}

    /**
     * // TODO: Will be replaced with a domain event from this module -- maybe agreement needs to know?
     * @param ReferralAgreementInvitee $invitee
     * @param RegisterUser             $form
     * @return User
     * @throws UsernameExistsException
     * @throws \PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @throws \PapaLocal\Entity\Exception\UserNotFoundException
     * @throws \ReflectionException
     */
	public function registerReferralAgreementInvitee(ReferralAgreementInvitee $invitee, RegisterUser $form)
	{
		// check if user exists for username
		if ($this->userRepository->userExists($form->getUsername())) {
			throw new UsernameExistsException(sprintf('An account exists for username %s.', $form->getUsername()));
		}

		// create user record
		$this->tableGateway->setTable('User');
		$userId = $this->tableGateway->create(array(
			'personId' => $invitee->getPerson()->getId(),
			'password' => $form->getPassword()
		));

		$this->tableGateway->setTable('L_UserRole');
		$roleRows = $this->tableGateway->findBy('name', AttrType::SECURITY_ROLE_USER);

		//user only gets ROLE_USER by default
		$this->tableGateway->setTable('R_UserApplicationRole');
		$ids['roleId'] = $this->tableGateway->create(array(
			'userId' => $userId,
			'roleId' => $roleRows[0]['id']
		));

		// save person->email relationship
		$this->tableGateway->setTable('L_EmailAddressType');
		$emailTypeRows = $this->tableGateway->findBy('description', AttrType::EMAIL_USERNAME);

		if (strcasecmp($form->getUsername(), $invitee->getEmailAddress()->getEmailAddress()) === 0) {
			// user registered with existing email
			$this->tableGateway->setTable('R_PersonEmailAddress');
			$existingEmailRow = $this->tableGateway->findByColumns(array(
				'personId' => $invitee->getPerson()->getId(),
				'emailId' => $invitee->getEmailAddress()->getId(),
			));

			unset($existingEmailRow[0]['id']);
			$existingEmailRow[0]['typeId'] = $emailTypeRows[0]['id'];
			$this->tableGateway->create($existingEmailRow[0]);

		} else {
			$this->tableGateway->setTable('EmailAddress');
			$emailId = $this->tableGateway->create(array('emailAddress' => $form->getUsername()));

			$this->tableGateway->setTable('R_PersonEmailAddress');
			$this->tableGateway->create(array(
				'personId' => $invitee->getPerson()->getId(),
				'emailId' => $emailId,
				'typeId' => $emailTypeRows[0]['id']
			));
		}

		$user = $this->userRepository->loadUser($form->getUsername());
		return $user;
	}

    /**
     * @param $username
     * @throws UserNotFoundException
     */
    public function findByUsername($username)
    {
        if(!$this->userRepository->userExists($username)) {
            throw new UserNotFoundException();
        }
        return;
    }

    /**
     * @param $guid
     */
    public function findByGuid(Guid $guid)
    {
        // TODO: Implement
        dump('User found');
    }

    public function createPerson(Guid $personId, PersonInterface $person)
    {
        // TODO: Implement
        dump('Create Person');
	}

    public function deletePerson(Guid $personId)
    {
        // TODO: Implement
        dump('Delete Person');
	}

}