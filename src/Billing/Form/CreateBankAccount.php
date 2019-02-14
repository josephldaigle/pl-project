<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/2/19
 * Time: 7:27 AM
 */

namespace PapaLocal\Billing\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * CreateBankAccount.
 *
 * @package PapaLocal\Billing\Form
 */
class CreateBankAccount
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "First name must be present."
     * )
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Last name must be present."
     * )
     *
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Customer ID must be present."
     * )
     */
    private $accountHolder;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Bank name must be present."
     * )
     */
    private $bankName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Routing number must be present."
     * )
     */
    private $routingNumber;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Account number must be present."
     * )
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Account type must be present."
     * )
     */
    private $accountType; // checking or savings

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "Address must be present."
     * )
     */
    private $address;

    /**
     * @var bool
     */
    private $isDefaultPayMethod;

    /**
     * CreateBankAccount constructor.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $accountHolder
     * @param string $bankName
     * @param string $routingNumber
     * @param string $accountNumber
     * @param string $accountType
     * @param array $address
     * @param bool   $isDefaultPayMethod
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $accountHolder,
        string $bankName,
        string $routingNumber,
        string $accountNumber,
        string $accountType,
        array $address,
        bool $isDefaultPayMethod = false
    )
    {
        $this->firstName          = $firstName;
        $this->lastName           = $lastName;
        $this->accountHolder      = $accountHolder;
        $this->bankName           = $bankName;
        $this->routingNumber      = $routingNumber;
        $this->accountNumber      = $accountNumber;
        $this->accountType        = $accountType;
        $this->address            = $address;
        $this->isDefaultPayMethod = $isDefaultPayMethod;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getRoutingNumber(): string
    {
        return $this->routingNumber;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    /**
     * @return bool
     */
    public function isDefaultPayMethod(): bool
    {
        return $this->isDefaultPayMethod;
    }
}