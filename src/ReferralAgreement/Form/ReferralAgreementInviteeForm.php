<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/7/18
 * Time: 11:47 AM
 */


namespace PapaLocal\ReferralAgreement\Form;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ReferralAgreementInviteeForm.
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class ReferralAgreementInviteeForm
{
    /**
     * @var Guid
     *
     * @Assert\NotBlank(
     *     message = "There was an unexpected error processing your request. Our support staff has been notified."
     *     )
     */
    private $agreementId;

	/**
	 * @var string
	 *
	 * @Assert\NotBlank(
	 *     message = "Please provide the recipient first name."
	 *     )
	 */
	private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide the recipient last name."
     *     )
     */
    private $lastName;

	/**
	 * @var string
	 *
	 * @Assert\NotBlank(
	 *     message = "Please provide a brief message."
	 *     )
	 */
	private $message;

    /**
     * @var EmailAddress
     *
     * @Assert\Valid
     */
    private $emailAddress;

    /**
     * @var PhoneNumber
     *
     * @Assert\Valid
     */
    private $phoneNumber;

    /**
     * ReferralAgreementInviteeForm constructor.
     *
     * @param Guid          $agreementId
     * @param string        $firstName
     * @param string        $lastName
     * @param string        $message
     * @param EmailAddress  $emailAddress
     * @param PhoneNumber   $phoneNumber
     */
    public function __construct(Guid $agreementId = null,
                                string $firstName = '',
                                string $lastName = '',
                                string $message = '',
                                EmailAddress $emailAddress = null,
                                PhoneNumber $phoneNumber = null)
    {
        $this->setAgreementId($agreementId);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setMessage($message);

        if (! is_null($emailAddress)) {
            $this->setEmailAddress($emailAddress);
        }

        if (! is_null($phoneNumber) && ! empty($phoneNumber->getPhoneNumber())) {
            $this->setPhoneNumber($phoneNumber);
        }
    }

    /**
     * @param Guid $agreementId
     */
    protected function setAgreementId(Guid $agreementId): void
    {
        $this->agreementId = $agreementId;
    }

    /**
     * @param string $firstName
     */
    protected function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    protected function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $message
     */
    protected function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param EmailAddress $emailAddress
     */
    protected function setEmailAddress(EmailAddress $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param PhoneNumber $phoneNumber
     */
    protected function setPhoneNumber(PhoneNumber $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getAgreementId()
    {
        return $this->agreementId;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
}