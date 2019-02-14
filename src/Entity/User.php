<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 3:39 PM
 */

namespace PapaLocal\Entity;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\Entity\UserInterface as EwebifyUserInterface;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\ValueObject\ContactProfile;
use PapaLocal\ValueObject\User\UserFeed;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use PapaLocal\Entity\Validation as AppAssert;


/**
 * Class User.
 *
 * @package PapaLocal\Entity
 *
 * Model a persisted user.
 */
class User extends Entity implements UserInterface, EquatableInterface, EwebifyUserInterface
{
    /**
     * @var int
     *
     * @Assert\Blank(
     *     message = "Id must be blank.",
     *     groups = {"create"}
     *     )
     *
     * @Assert\NotBlank(
     *     message = "Id must be present.",
     *     groups = {"update"}
     *     )
     *
     */
    private $id;

    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * @var bool
     *
     * @Assert\Blank(
     *     message = "isActive must be blank.",
     *     groups = {"create"}
     *     )
     *
     */
    private $isActive;

    /**
     * @var int
     */
    private $notificationSavePoint;

    /**
     * @var string
     *
     * @AppAssert\PasswordConstraint(
     *     groups={"create"}
     *     )
     *
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *      message = "Time zone cannot be blank.",
     *      groups = {"create"}
     *     )
     *
     */
    private $timeZone;

    /**
     * @var string
     *
     *  @Assert\Blank(
     *     message = "Time created must be blank.",
     *     groups = {"create", "update"}
     *     )
     *
     */
    private $timeCreated;

    /**
     * @var Person
     *
     * @Assert\Valid
     */
    private $person;

    /**
     * @var string username
     *
     * @Assert\Email(
     *     message = "The email address is invalid.",
     *     groups={"create"}
     *     )
     *
     * @Assert\NotBlank(
     *     message = "Username cannot be blank.",
     *     groups = {"create"}
     *     )
     *
     *
     */
    private $username;

    /**
     * @var $salt
     */
    private $salt;

    /**
     * @var $roles
     *
     * @Assert\Blank(
     *    message = "Roles must be blank.",
     *    groups = {"create"}
     *    )
     */
    private $roles;

    /**
     * @var Collection a list of the companies the user owns
     */
    private $companyList;

    /**
     * @var ContactProfile
     */
    private $contactProfile;

    /**
     * @var UserFeed
     */
    private $userFeed;

    /**
     * @var BillingProfile
     */
    private $billingProfile;

	/**
	 * @var NotificationList
	 */
    private $notificationList;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     *
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param GuidInterface $guid
     *
     * @return User
     */
    public function setGuid(GuidInterface $guid): User
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @param Person $person
     *
     * @return User
     */
    public function setPerson(Person $person): User
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFirstName()
    {
        return $this->person->getFirstName();
    }

    /**
     * @inheritDoc
     */
    public function getLastName()
    {
        return $this->person->getLastName();
    }

    /**
     * @inheritDoc
     */
    public function getAbout()
    {
        return $this->person->getAbout();
    }


    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (is_null($this->isActive)) ? false : $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getNotificationSavePoint()
    {
        return $this->notificationSavePoint;
    }

    /**
     * @param int $notificationSavePoint
     * @return User
     */
    public function setNotificationSavePoint(int $notificationSavePoint): User
    {
        $this->notificationSavePoint = $notificationSavePoint;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set timeCreated
     *
     * @param string $timeCreated
     *
     * @return User
     */
    public function setTimeCreated($timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /**
     * Get timeCreated
     *
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * Set timeZone
     *
     * @param string $timeZone
     *
     * @return User
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * UserInterface
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCompanyList()
    {
        return $this->companyList;
    }

    /**
     * @param Collection $companyList
     *
     * @return User
     */
    public function setCompanyList(Collection $companyList): User
    {
        $this->companyList = $companyList;
        return $this;
    }

    /**
     * @return ContactProfile
     */
    public function getContactProfile()
    {
        return $this->contactProfile;
    }

    /**
     * @param ContactProfile $contactProfile
     *
     * @return User
     */
    public function setContactProfile(ContactProfile $contactProfile): User
    {
        $this->contactProfile = $contactProfile;

        return $this;
    }

    /**
     * @return UserFeed
     */
    public function getUserFeed()
    {
        return $this->userFeed;
    }

    /**
     * @param UserFeed $userFeed
     *
     * @return User
     */
    public function setUserFeed(UserFeed $userFeed): User
    {
        $this->userFeed = $userFeed;
        return $this;
    }

    /**
     * @param BillingProfile $profile
     * @return User
     */
    public function setBillingProfile(BillingProfile $profile): User
    {
        $this->billingProfile = $profile;
        return $this;
    }

    /**
     * @return BillingProfile
     */
    public function getBillingProfile()
    {
        return $this->billingProfile;
    }

	/**
	 * @return NotificationList
	 */
	public function getNotificationList(): NotificationList
	{
		return $this->notificationList;
	}

	/**
	 * @param NotificationList $notificationList
	 *
	 * @return User
	 */
	public function setNotificationList( NotificationList $notificationList ): User
	{
		$this->notificationList = $notificationList;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * @see UserInterface
	 */
    public function eraseCredentials()
    {
        $this->password = null;
    }

    /**
     * {@inheritdoc}
     * @see UserInterface
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (strcasecmp($this->username, $user->getUsername()) !== 0) {
            return false;
        }

        return true;
    }
}

