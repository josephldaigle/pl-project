<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/3/18
 * Time: 10:08 AM
 */


namespace PapaLocal\ReferralAgreement\Entity;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\Guid;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ReferralAgreementInvitee
 *
 * Model an invitee for a referral agreement.
 *
 * @package PapaLocal\ReferralAgreement\Entity
 */
class ReferralAgreementInvitee
{
    const INITIAL_WORKFLOW_PLACE = 'Initialized';

    /**
     * @var Guid
     */
    private $guid;

    /**
     * @var string this is the marker for the agreement_invitee workflow.
     */
    private $currentPlace;

	/**
	 * @var Guid
	 */
	private $agreementId;

    /**
     * @var string
     */
	private $firstName;

    /**
     * @var string
     */
	private $lastName;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var EmailAddress
	 */
	private $emailAddress;

    /**
     * @var PhoneNumber
     */
	private $phoneNumber;

    /**
     * @var Guid
     */
	private $userId;

    /**
     * @var bool
     */
	private $isParticipant;

    /**
     * @var bool
     */
	private $isDeclined;

    /**
     * @var string
     */
	private $timeNotified;

    /**
     * ReferralAgreementInvitee constructor.
     *
     * @param Guid             $guid
     * @param Guid             $agreementId
     * @param string           $firstName
     * @param string           $lastName
     * @param string           $message
     * @param EmailAddress     $emailAddress
     * @param PhoneNumber|null $phoneNumber
     */
    public function __construct(
        Guid $guid,
        Guid $agreementId,
        string $firstName,
        string $lastName,
        string $message,
        EmailAddress $emailAddress,
        PhoneNumber $phoneNumber = null)
    {
        $this->guid = $guid;
        $this->agreementId = $agreementId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->message = $message;
        $this->emailAddress = $emailAddress;

        if (! is_null($phoneNumber)) {
            $this->setPhoneNumber($phoneNumber);
        }

        // set initial place
        $this->setCurrentPlace(self::INITIAL_WORKFLOW_PLACE);
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
     * @return ReferralAgreementInvitee
     */
    public function setGuid(Guid $guid): ReferralAgreementInvitee
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    /**
     * @param string $currentPlace
     *
     * @return ReferralAgreementInvitee
     */
    public function setCurrentPlace(string $currentPlace): ReferralAgreementInvitee
    {
        $this->currentPlace = $currentPlace;
        return $this;
    }


    /**
     * @return Guid
     */
    public function getAgreementId(): Guid
    {
        return $this->agreementId;
    }

    /**
     * @param Guid $agreementId
     *
     * @return ReferralAgreementInvitee
     */
    public function setAgreementId(Guid $agreementId): ReferralAgreementInvitee
    {
        $this->agreementId = $agreementId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ReferralAgreementInvitee
     */
    public function setMessage(string $message): ReferralAgreementInvitee
    {
        $this->message = $message;

        return $this;
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
     * @return ReferralAgreementInvitee
     */
    public function setEmailAddress(EmailAddress $emailAddress): ReferralAgreementInvitee
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return ReferralAgreementInvitee
     */
    public function setPhoneNumber(PhoneNumber $phoneNumber): ReferralAgreementInvitee
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return ($this->userId instanceof Guid && !empty($this->userId->value())) ? true : false;
    }

    /**
     * @return Guid
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param Guid $userId
     *
     * @return ReferralAgreementInvitee
     */
    public function setUserId(Guid $userId): ReferralAgreementInvitee
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isParticipant(): bool
    {
        return (! is_null($this->isParticipant)) ? (bool)$this->isParticipant : false;
    }

    /**
     * @param bool $isParticipant
     *
     * @return ReferralAgreementInvitee
     */
    public function setIsParticipant(bool $isParticipant): ReferralAgreementInvitee
    {
        $this->isParticipant = $isParticipant;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return (!is_null($this->isDeclined)) ? $this->isDeclined : false;
    }

    /**
     * @param bool $isDeclined
     *
     * @return ReferralAgreementInvitee
     */
    public function setIsDeclined(bool $isDeclined): ReferralAgreementInvitee
    {
        $this->isDeclined = $isDeclined;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeNotified()
    {
        return $this->timeNotified;
    }

    /**
     * @param string $timeNotified
     *
     * @return ReferralAgreementInvitee
     */
    public function setTimeNotified(string $timeNotified): ReferralAgreementInvitee
    {
        $this->timeNotified = $timeNotified;

        return $this;
    }
}