<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 11:18 PM
 */

namespace PapaLocal\ReferralAgreement\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class CreateAgreementForm
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class CreateAgreementForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please give the agreement a name."
     *     )
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide a brief description."
     *     )
     */
    private $description;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Please provide a quantity."
     *     )
     */
    private $quantity;

    /**
     * @var string
     *
     * @Assert\Choice({"weekly", "monthly"}, strict = true, message = "Invalid strategy provided.")
     *
     */
    private $strategy;

    /**
     * @var float
     *
     * @Assert\NotBlank(
     *     message = "Please provide a price."
     *     )
     */
    private $bid;

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "At least one location must be included."
     * )
     *
     * @Assert\Valid(
     *     traverse = true
     * )
     */
    private $includedLocations;

    /**
     * @var array
     *
     * @Assert\Valid(
     *     traverse = true
     * )
     */
    private $excludedLocations;

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "At least one service must be included."
     * )
     *
     * @Assert\Valid(
     *     traverse = true
     * )
     */
    private $includedServices;

    /**
     * @var array
     *
     * @Assert\Valid(
     *     traverse = true
     * )
     */
    private $excludedServices;

    /**
     * CreateAgreementForm constructor.
     *
     * @param string $name
     * @param string $description
     * @param int    $quantity
     * @param string $strategy
     * @param float  $bid
     * @param array  $includedLocations
     * * @param array  $includedServices
     * @param array  $excludedLocations
     * @param array  $excludedServices
     */
    public function __construct(
        string $name,
        string $description,
        int $quantity,
        string $strategy,
        float $bid,
        array $includedLocations,
        array $includedServices,
        array $excludedLocations = [],
        array $excludedServices = []
    )
    {
        $this->name              = $name;
        $this->description       = $description;
        $this->quantity          = $quantity;
        $this->strategy          = $strategy;
        $this->bid               = $bid;
        $this->includedLocations = $includedLocations;
        $this->includedServices  = $includedServices;
        $this->excludedLocations = $excludedLocations;
        $this->excludedServices  = $excludedServices;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return $this->strategy;
    }

    /**
     * @return float
     */
    public function getBid(): float
    {
        return $this->bid;
    }

    /**
     * @return array
     */
    public function getIncludedLocations(): array
    {
        return $this->includedLocations;
    }

    /**
     * @return array
     */
    public function getExcludedLocations(): array
    {
        return $this->excludedLocations;
    }

    /**
     * @return array
     */
    public function getIncludedServices(): array
    {
        return $this->includedServices;
    }

    /**
     * @return array
     */
    public function getExcludedServices(): array
    {
        return $this->excludedServices;
    }
}