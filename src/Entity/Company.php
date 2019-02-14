<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 3:39 PM
 */


namespace PapaLocal\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ValueObject\ContactProfile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Company.
 *
 */
class Company extends Entity
{
    /**
     * Company account status codes.
     */
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_DEACTIVATED = 'Deactivated';

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
     */
    private $id;

    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * @var Guid
     */
    private $ownerGuid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Name cannot be blank.",
     *     groups = {"create", "update_name"}
     *     )
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Description (about) cannot be blank.",
     *     groups={"save_about"}
     * )
     * @Assert\Length(
     *     min=10,
     *     max=250,
     *     minMessage="Description (about) is too short.",
     *     maxMessage="Description (about) is too long.",
     *     groups={"save_about"}
     * )
     *
     */
    private $about;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Website cannot be blank.",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Length(
     *      max=200,
     *      maxMessage="Website cannot contain more than 200 characters.",
     *      groups={"save_website"}
     * )
     *
     * Only accept  urls formatted as http://www or https://www.
     * Also covers minimum required format (http://a.nm), or 15 chars long.
     * @Assert\Regex(
     *     pattern="/(www)/",
     *     message="The website provided is not in an acceptable format. Please include the www prefix.",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/",
     *     message="The website provided is not in an acceptable format. Please include a suffix(.com, .org, .biz).",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^(http:\/\/|https:\/\/)(w{3}(\.){1}){0,1}[a-z0-9]+([\-\.]{1}[a-z0-9]+)*(\.{0,1})([a-z]{2,5}(:[0-9]{1,5})?(\/.*)?)?$/",
     *     message="The website provided is not in an acceptable format.",
     *     groups={"save_website"}
     * )
     */
    private $website;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Date founded cannot be blank.",
     *     groups = {"save_founded_date"}
     *     )
     *
     * This regex defines a range between 1900 to 2199
     * @Assert\Regex(
     *     pattern = "/^(190\d|19\d\d|200\d|20\d\d|21\d\d)$/",
     *     message = "Date provided is out of range",
     *     groups = {"save_founded_date"}
     *    )
     *
     */
    private $dateFounded;

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
     * @var string
     *
     * @Assert\Choice(
     *     strict = true,
     *     callback = "getStatuses",
     *     message = "Invalid value supplied for status field.",
     *     groups = {"create", "save_status"},
     * )
     */
    private $status;

    /**
     * @var ContactProfile
     */
    private $contactProfile;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var  PhoneNumber
     */
    private $phoneNumber;

    /**
     * @var Address
     */
    private $address;

    /**
     * Get company id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set company id
     *
     * @param int $id
     * @return Company id
     */
    public function setId(int $id): Company
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface
    {
        return $this->guid;
    }

    /**
     * @param GuidInterface $guid
     *
     * @return Company
     */
    public function setGuid(GuidInterface $guid): Company
    {
        $this->guid = $guid;
        return $this;
    }

    /**
     * @return Guid
     */
    public function getOwnerGuid(): Guid
    {
        return $this->ownerGuid;
    }

    /**
     * @param Guid $ownerGuid
     *
     * @return Company
     */
    public function setOwnerGuid(Guid $ownerGuid): Company
    {
        $this->ownerGuid = $ownerGuid;
        return $this;
    }

    /**
     * Get company name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set company name
     *
     * @param string $name
     * @return Company
     */
    public function setName(string $name): Company
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get company about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set company about
     *
     * @param string $about
     * @return Company about
     */
    public function setAbout(string $about): Company
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get company Date Founded
     *
     * @return int
     */
    public function getDateFounded()
    {
        return $this->dateFounded;
    }

    /**
     * Set company Date Founded
     *
     * @param int $dateFounded
     * @return Company Date Founded
     */
    public function setDateFounded(int $dateFounded): Company
    {
        $this->dateFounded = $dateFounded;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return Company
     */
    public function setWebsite(string $website): Company
    {
        $this->website = $website;
        return $this;
    }


    /**
     * Get company Time Created
     *
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * Set company time created
     *
     * @param string $timeCreated
     * @return Company time created
     */
    public function setTimeCreated(string $timeCreated): Company
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /**
     * Get company Time Updated
     *
     * @return string
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * Set company time updated
     *
     * @param string $timeUpdated
     * @return Company time updated
     */
    public function setTimeUpdated(string $timeUpdated): Company
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return [self::STATUS_ACTIVE, self::STATUS_DEACTIVATED];
    }

    /**
     * @param string $status
     *
     * @return Company
     */
    public function setStatus(string $status): Company
    {
        $this->status = $status;
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
     * @return Company
     */
    public function setContactProfile(ContactProfile $contactProfile): Company
    {
        $this->contactProfile = $contactProfile;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return Company
     */
    public function setAddress(Address $address): Company
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFeedType()
    {
        return 'company';
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * @param EmailAddress $emailAddress
     *
     * @return Company
     */
    public function setEmailAddress(EmailAddress $emailAddress): Company
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return PhoneNumber
     */
    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return Company
     */
    public function setPhoneNumber(PhoneNumber $phoneNumber): Company
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
