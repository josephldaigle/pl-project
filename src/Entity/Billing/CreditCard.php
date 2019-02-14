<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/17/17
 * Time: 3:41 PM
 */

namespace PapaLocal\Entity\Billing;

use PapaLocal\Entity\AddressInterface;
use PapaLocal\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use PapaLocal\Entity\Validation as AppAssert;

/**
 * CreditCard.
 *
 * @package PapaLocal\Entity\Billing
 */
class CreditCard extends Entity implements CreditCardInterface
{
    const CARD_TYPE_VISA = 'Visa';
    const CARD_TYPE_MASTER = 'Master';
    const CARD_TYPE_DISCOVER = 'Discover';
    const CARD_TYPE_AMEX = 'American Express';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string account holder
     *
     * @Assert\NotBlank(
     *     message = "First name must be present.",
     *     groups = {"create"}
     * )
     */
    private $firstName;

    /**
     * @var string account holder
     *
     * @Assert\NotBlank(
     *     message = "Last name must be present.",
     *     groups = {"create"}
     * )
     */
    private $lastName;


    /**
     * @var int AuthNet customerId
     *
     * @Assert\NotBlank(
     *     message = "Customer ID must be present.",
     *     groups = {"update"}
     * )
     */
    private $customerId;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Card number must be present.",
     *     groups = {"create"}
     * )
     *
     * @Assert\CardScheme(
     *     schemes={"VISA", "MASTERCARD", "DISCOVER", "AMEX", },
     *     message="Your credit card number is invalid.",
     *     groups = {"create"}
     * )
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Card type must be present.",
     *     groups = {"create"}
     * )
     */
    private $cardType;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Expiration month must be present.",
     *     groups = {"create"}
     * )
     *
     * @Assert\Range(
     *      min = 1,
     *      max = 12,
     *      minMessage = "Month must start at {{ limit }}.",
     *      maxMessage = "Month cannot be more than {{ limit }}.",
     *      groups = {"create"}
     * )
     *
     * @AppAssert\ExpirationMonthConstraint(
     *     groups={"create"}
     * )
     *
     */
    private $expirationMonth;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Expiration year must be present.",
     *     groups = {"create"}
     * )
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 2,
     *      exactMessage = "The expiration year must be at exactly {{ limit }} digits long.",
     *      groups = {"create"}
     * )
     *
     * @AppAssert\ExpirationYearConstraint(
     *     groups={"create"}
     * )
     *
     */
    private $expirationYear;

    /**
     * @var int
     *
     *@Assert\NotBlank(
     *     message = "Card security code must be present.",
     *     groups = {"create"}
     * )
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 4,
     *      minMessage = "The security code must be at least {{ limit }} digits long.",
     *      maxMessage = "The security code must be at most {{ limit }} digits long.",
     *      groups = {"create"}
     * )
     */
    private $securityCode;

    /**
     * @var AddressInterface
     *
     * @Assert\NotBlank(
     *     message = "Address must be present.",
     *     groups = {"create"}
     * )
     */
    private $address;

    /**
     * @var bool
     */
    private $isDefaultPayMethod;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CreditCard
     */
    public function setId(int $id): CreditCard
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return CreditCard
     */
    public function setFirstName(string $firstName): CreditCard
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return CreditCard
     */
    public function setLastName(string $lastName): CreditCard
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return CreditCard
     */
    public function setCustomerId(int $customerId): CreditCard
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param int $cardNumber
     *
     * @return CreditCard
     */
    public function setCardNumber(string $cardNumber): CreditCard
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     *
     * @return CreditCard
     */
    public function setCardType(string $cardType): CreditCard
    {
        $this->cardType = $cardType;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationMonth()
    {
//        return date('m', $this->expirationMonth);
        return $this->expirationMonth;
    }

    /**
     * @param int $expirationMonth
     *
     * @return CreditCard
     */
    public function setExpirationMonth(int $expirationMonth): CreditCard
    {
        $this->expirationMonth = $expirationMonth;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationYear()
    {
//        return date('y', $this->expirationYear);
        return $this->expirationYear;
    }

    /**
     * @param int $expirationYear
     *
     * @return CreditCard
     */
    public function setExpirationYear(int $expirationYear): CreditCard
    {
        $this->expirationYear = $expirationYear;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->getExpirationMonth() . $this->getExpirationYear();
    }

    /**
     * @return int
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * @param int $securityCode
     *
     * @return CreditCard
     */
    public function setSecurityCode(int $securityCode): CreditCard
    {
        $this->securityCode = $securityCode;
        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    /**
     * @param AddressInterface $address
     *
     * @return CreditCard
     */
    public function setAddress(AddressInterface $address): CreditCard
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentType()
    {
        return self::class;
    }

    /**
     * @inheritdoc
     */
    public function getAccountNumber()
    {
        return $this->getCardNumber();
    }

    /**
     * @return bool
     */
    public function isDefaultPayMethod(): bool
    {
        return (is_null($this->isDefaultPayMethod)) ? false : $this->isDefaultPayMethod;
    }

    /**
     * @param bool $isDefaultPayMethod
     *
     * @return CreditCard
     */
    public function setIsDefaultPayMethod(bool $isDefaultPayMethod): CreditCard
    {
        $this->isDefaultPayMethod = $isDefaultPayMethod;
        return $this;
    }

	public function sameAsAccount(PaymentAccountInterface $account): bool
	{
		if (! get_class($this) === get_class($account)) {
			return false;
		}

		if ($this->getCardNumber() === $account->getCardNumber()
			&& $this->getCardType() === $account->getCardType()
			&& $this->getCustomerId() === $account->getCustomerId()) {

		}
	}


}