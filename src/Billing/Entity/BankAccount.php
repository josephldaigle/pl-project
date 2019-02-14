<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/19/17
 * Time: 8:17 PM
 */


namespace PapaLocal\Billing\Entity;


use PapaLocal\Core\ValueObject\Address;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class BankAccount
 *
 * @package PapaLocal\Billing\Entity
 */
class BankAccount implements BankAccountInterface
{
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
     * @var int account holder
     *
     * @Assert\NotBlank(
     *     message = "Customer ID must be present.",
     *     groups = {"create"}
     * )
     */
    private $customerId;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Bank name must be present.",
     *     groups = {"create"}
     * )
     */
    private $bankName;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Routing number must be present.",
     *     groups = {"create"}
     * )
     */
    private $routingNumber;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Account number must be present.",
     *     groups = {"create"}
     * )
     */
    private $accountNumber;

    /**
     * @var string name on the account
     */
    private $accountHolder;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Account type must be present.",
     *     groups = {"create"}
     * )
     */
    private $accountType;

    /**
     * @var Address
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return BankAccount
     */
    public function setFirstName(string $firstName): BankAccount
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return BankAccount
     */
    public function setLastName(string $lastName): BankAccount
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
     * @return BankAccount
     */
    public function setCustomerId(int $customerId): BankAccount
    {
        $this->customerId = $customerId;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     *
     * @return BankAccount
     */
    public function setBankName(string $bankName): BankAccount
    {
        $this->bankName = $bankName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

    /**
     * @param string $routingNumber
     *
     * @return BankAccount
     */
    public function setRoutingNumber(string $routingNumber): BankAccount
    {
        $this->routingNumber = $routingNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return BankAccount
     */
    public function setAccountNumber(string $accountNumber): BankAccount
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    /**
     * @param string $accountHolder
     *
     * @return BankAccount
     */
    public function setAccountHolder(string $accountHolder): BankAccount
    {
        $this->accountHolder = $accountHolder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     *
     * @return BankAccount
     */
    public function setAccountType(string $accountType): BankAccount
    {
        $this->accountType = $accountType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return BankAccount
     */
    public function setAddress(Address $address): BankAccount
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultPayMethod(): bool
    {
        return $this->isDefaultPayMethod;
    }

    /**
     * @param bool $isDefaultPayMethod
     *
     * @return BankAccount
     */
    public function setIsDefaultPayMethod(bool $isDefaultPayMethod): BankAccount
    {
        $this->isDefaultPayMethod = $isDefaultPayMethod;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentType()
    {
        return self::class;
    }

}