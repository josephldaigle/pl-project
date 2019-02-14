<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 3:39 PM
 */

namespace PapaLocal\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ValueObject\ContactProfile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation as Serialize;


/**
 * Person
 */
class Person extends Entity implements PersonInterface
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
     * @var Guid
     */
    private $guid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "First name cannot be blank.",
     *     groups = {"create", "update_first_name"}
     *     )
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Last name cannot be blank.",
     *     groups = {"create", "update_last_name"}
     *     )
     */
    private $lastName;

    /**
     * @var string
     */
    private $about;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time created must be blank.",
     *     groups = {"create", "update"}
     *     )
     */
    private $timeCreated;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time updated must be blank.",
     *     groups = {"create", "update"}
     *     )
     */
    private $timeUpdated;

	/**
	 * @var ContactProfile
	 */
	private $contactProfile;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): Person
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Guid
     */
    public function getGuid(): Guid
    {
        return $this->guid;
    }

    /**
     * @param Guid $guid
     *
     * @return Person
     */
    public function setGuid(Guid $guid): Person
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Person
     */
    public function setFirstName(string $firstName): Person
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Person
     */
    public function setLastName(string $lastName): Person
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return Person
     */
    public function setAbout(string $about): Person
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set timeCreated
     *
     * @param string $timeCreated
     *
     * @return Person
     */
    public function setTimeCreated(string $timeCreated): Person
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
     * @return string
     */
    public function getTimeUpdated(): string
    {
        return $this->timeUpdated;
    }

    /**
     * @param string $timeUpdated
     *
     * @return Person
     */
    public function setTimeUpdated(string $timeUpdated): Person
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

	/**
	 * @return ContactProfile
	 */
	public function getContactProfile(): ContactProfile
	{
		return $this->contactProfile;
	}

	/**
	 * @param ContactProfile $contactProfile
	 *
	 * @return Person
	 */
	public function setContactProfile(ContactProfile $contactProfile): Person
	{
		$this->contactProfile = $contactProfile;

		return $this;
	}

    /**
     * Convert object to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = array();

        foreach(get_object_vars($this) as $key => $val) {
            if (! is_null($val) && ! empty($val)) {
                $array[$key] = $val;
            }
        }

        return $array;
    }
}

