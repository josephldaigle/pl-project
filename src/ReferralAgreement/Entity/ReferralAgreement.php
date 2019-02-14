<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/10/18
 * Time: 8:35 PM
 */


namespace PapaLocal\ReferralAgreement\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\FeedItemInterface;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use PapaLocal\Core\Validation\DoesNotExist;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ReferralAgreement
 *
 * Model a referral agreement.
 *
 * @package PapaLocal\ReferralAgreement\Entity
 */
class ReferralAgreement implements ReferralAgreementInterface, FeedItemInterface
{
    const INITIAL_WORKFLOW_PLACE = 'Initialized';

    /**
     * @var string  this is the marker for the referral_agreement workflow.
     */
    private $currentPlace;

    /**
     * @var Guid
     *
     * @Assert\NotBlank(
     *     message = "GUID must be present.",
     *     groups = {"create"}
     *     )
     */
    private $guid;

    /**
     * @var Guid
     *
     * @Assert\NotBlank(
     *     message = "Company guid cannot be blank.",
     *     groups = {"create"}
     * )
     */
    private $companyGuid;

	/**
	 * @var string
	 *
	 * @Assert\NotBlank(
	 *     message = "Name cannot be blank.",
	 *     groups = {"create", "update_name"}
	 * )
	 *
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @Assert\NotBlank(
	 *     message = "Description cannot be blank.",
	 *     groups = {"create", "update_description"}
	 * )
	 *
	 */
	private $description;

    /**
	 * @var int
	 *
	 * @Assert\NotBlank(
	 *     message = "Quantity cannot be blank.",
	 *     groups = {"create", "update_quantity"}
	 * )
	 */
	private $quantity;

    /**
	 * @var string
     *
     * @Assert\NotBlank(
     *     message = "Strategy cannot be blank.",
     *     groups = {"create"}
     * )
	 */
	private $strategy;

    /**
	 * @var float (2 pt decimal)
     *
     * @Assert\NotBlank(
     *     message = "Bid cannot be blank.",
     *     groups = {"create"}
     * )
	 */
	private $bid;

    /**
     * @var StatusHistory
     */
    private $statusHistory;

    /**
     * @var Guid
     */
    private $ownerGuid;

    /**
     * @var int
     */
    private $referralCount;

	/**
	 * @var Collection
	 *
	 * @Assert\Count(
     *     min = 1,
     *     minMessage = "At least one location must be included.",
     *     groups = {"update_included_locations"}
     * )
     * @Assert\Valid(
     *     traverse = true,
     *     groups={"update_included_locations"}
     * )
	 */
	private $includedLocations;

	/**
	 * @var Collection
	 *
     * @Assert\Valid(
     *     traverse = true,
     *     groups={"update_excluded_locations"}
     * )
	 */
	private $excludedLocations;

	/**
	 * @var Collection
	 *
     * @Assert\Count(
     *     min = 1,
     *     minMessage = "At least one service must be included.",
     *     groups = {"update_included_services"}
     * )
	 * @Assert\Valid(
     *     traverse = true,
	 *     groups={"update_included_services"}
	 * )
	 */
	private $includedServices;

	/**
	 * @var Collection
	 *
	 * @Assert\Valid(
     *     traverse = true,
	 *     groups={"update_excluded_services"}
	 * )
	 */
	private $excludedServices;

	/**
	 * @var Collection
	 *
	 * @Assert\Valid(
	 *     groups={"update_invitees"}
	 * )
	 */
	private $invitees;

    /**
     * @var int
     */
	private $numberParticipants;

    /**
     * @var string
     */
	private $timeCreated;

    /**
     * ReferralAgreement constructor.
     *
     * @param Guid      $guid
     * @param Guid      $companyGuid
     * @param string    $name
     * @param string    $description
     * @param int       $quantity
     * @param string    $strategy
     * @param float     $bid
     * @param Guid|null $ownerGuid
     */
    public function __construct(
        Guid $guid,
        Guid $companyGuid,
        string $name,
        string $description,
        int $quantity,
        string $strategy,
        float $bid,
        Guid $ownerGuid = null
    )
    {
        $this->setGuid($guid);
        $this->setCompanyGuid($companyGuid);
        $this->setName($name);
        $this->setDescription($description);
        $this->setQuantity($quantity);
        $this->setStrategy($strategy);
        $this->setBid($bid);

        is_null($ownerGuid) ?: $this->setOwnerGuid($ownerGuid);
        $this->numberParticipants = 0;

        $this->setCurrentPlace(self::INITIAL_WORKFLOW_PLACE);
        $this->setReferralCount(0);
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
     * @return ReferralAgreement
     */
    public function setGuid(Guid $guid): ReferralAgreement
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return Guid
     */
    public function getCompanyGuid(): Guid
    {
        return $this->companyGuid;
    }

    /**
     * @param Guid $companyGuid
     *
     * @return ReferralAgreement
     */
    public function setCompanyGuid(Guid $companyGuid): ReferralAgreement
    {
        $this->companyGuid = $companyGuid;

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
	 * @param mixed $currentPlace
	 *
	 * @return ReferralAgreement
	 */
	public function setCurrentPlace($currentPlace)
	{
	    // stored value is always a string for compatibility w/ Symfony Workflow
	    if ($currentPlace instanceof Status) {
            $this->currentPlace = $currentPlace->getValue();
        } else {
	        $this->currentPlace = $currentPlace;
        }

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return ReferralAgreement
	 */
	public function setName(string $name): ReferralAgreement
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return ReferralAgreement
	 */
	public function setDescription(string $description): ReferralAgreement
	{
		$this->description = $description;

		return $this;
	}

    /**
     * @return StatusHistory
     */
    public function getStatusHistory(): StatusHistory
    {
        return $this->statusHistory;
    }

    /**
     * @param StatusHistory $statusHistory
     * @return ReferralAgreement
     */
    public function setStatusHistory(StatusHistory $statusHistory): ReferralAgreement
    {
	    // add status history to agreement
	    $this->statusHistory = $statusHistory;

        $currentStatus = $this->statusHistory->getCurrentStatus();

        if (! empty($currentStatus->getTimeUpdated())) {
            // the status is being added to load an agreement
            if ($currentStatus->getStatus() == Status::ACTIVE()) {
                $this->currentPlace = Status::ACTIVE()->getValue();
            } else {
                $this->currentPlace = ($currentStatus->getReason() ==  StatusChangeReason::CREATED())
                    ? StatusChangeReason::CREATED()->getValue()
                    : Status::INACTIVE()->getValue();
            }
        }

        return $this;
    }

    /**
     * Adds a status row to the agreement's history, without updating the object's internal state. This
     * is necessary to be able to use the workflow to manage the agreement's state.
     *
     * @param AgreementStatus $status
     */
    public function updateStatus(AgreementStatus $status)
    {
        $this->statusHistory->add($status);
    }

	/**
	 * @return int
	 */
	public function getQuantity(): int
	{
		return $this->quantity;
	}

	/**
	 * @param int $quantity
	 *
	 * @return ReferralAgreement
	 */
	public function setQuantity(int $quantity): ReferralAgreement
	{
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStrategy(): string
	{
		return $this->strategy;
	}

	/**
	 * @param string $strategy
	 *
	 * @return ReferralAgreement
	 */
	public function setStrategy(string $strategy): ReferralAgreement
	{
		$this->strategy = $strategy;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getBid(): float
	{
		return $this->bid;
	}

	/**
	 * @param float $bid
	 *
	 * @return ReferralAgreement
	 */
	public function setBid(float $bid): ReferralAgreement
	{
		$this->bid = $bid;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getIncludedLocations(): Collection
	{
		return $this->includedLocations;
	}

	/**
	 * @param Collection $includedLocations
	 *
	 * @return ReferralAgreement
	 */
	public function setIncludedLocations(Collection $includedLocations): ReferralAgreement
	{
		$this->includedLocations = $includedLocations;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExcludedLocations(): Collection
	{
		return $this->excludedLocations;
	}

	/**
	 * @param Collection $excludedLocations
	 *
	 * @return ReferralAgreement
	 */
	public function setExcludedLocations(Collection $excludedLocations)
	{
		$this->excludedLocations = $excludedLocations;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getIncludedServices(): Collection
	{
		return $this->includedServices;
	}

	/**
	 * @param Collection $includedServices
	 *
	 * @return ReferralAgreement
	 */
	public function setIncludedServices(Collection $includedServices): ReferralAgreement
	{
		$this->includedServices = $includedServices;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getExcludedServices(): Collection
	{
		return $this->excludedServices;
	}

	/**
	 * @param Collection $excludedServices
	 *
	 * @return ReferralAgreement
	 */
	public function setExcludedServices(Collection $excludedServices): ReferralAgreement
	{
		$this->excludedServices = $excludedServices;

		return $this;
	}

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     */
    public function setTimeCreated(string $timeCreated): void
    {
        $this->timeCreated = $timeCreated;
    }

	/**
	 * @return mixed
	 */
	public function getInvitees()
	{
		return $this->invitees;
	}

	/**
	 * @param Collection $invitees
	 *
	 * @return ReferralAgreement
	 */
	public function setInvitees(Collection $invitees): ReferralAgreement
	{
	    // set invitees
        $this->invitees = $invitees;

        // set number participants
        $participantCount = 0;
        foreach($invitees as $invitee) {
            if ($invitee->isParticipant()) {
                $participantCount++;
            }
        }
        $this->setNumberParticipants($participantCount);

        return $this;
	}

    /**
     * @return int
     */
    public function getNumberInvitees(): int
    {
        return (is_null($this->invitees)) ? 0 : $this->invitees->count();
    }

    /**
     * @return int
     */
    public function getNumberParticipants(): int
    {
        return $this->numberParticipants;
    }

    /**
     * @param int $numberParticipants
     * @return ReferralAgreement
     */
    protected function setNumberParticipants(int $numberParticipants): ReferralAgreement
    {
        $this->numberParticipants = $numberParticipants;
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
     * @return ReferralAgreement
     */
    public function setOwnerGuid(Guid $ownerGuid): ReferralAgreement
    {
        $this->ownerGuid = $ownerGuid;
        return $this;
    }

    /**
     * @param string $userGuid
     * @return bool
     */
	public function isOwner(string $userGuid)
    {
        return ($userGuid === $this->getOwnerGuid()->value());
    }

    /**
     * @param string $userGuid
     *
     * @return bool
     */
    public function isParticipant(string $userGuid)
    {
        if (is_null($this->invitees) || $this->invitees->count() < 1) {
            return false;
        }

        $found = $this->invitees->findBy('userId', new Guid($userGuid));

        return ($found && $found->isParticipant()) ? true : false;
    }

    /**
     * Whether or not the given userId is an invitee of this agreement.
     *
     * @param string $userId
     *
     * @return bool
     */
    public function isInvitee(string $userId)
    {
        if (is_null($this->invitees) || $this->invitees->count() < 1) {
            return false;
        }

        $found = $this->invitees->findBy('userId', new Guid($userId));

        return (is_null($found)) ? false : true;
    }

    /**
     * @return int
     */
    public function getReferralCount(): int
    {
        return $this->referralCount;
    }

    /**
     * @param int $referralCount
     *
     * @return ReferralAgreement
     */
    public function setReferralCount(int $referralCount): ReferralAgreement
    {
        $this->referralCount = $referralCount;

        return $this;
    }

    public function getId(): int
    {
        return 0;
    }

    public function getTitle(): string
    {
        return $this->name;
    }

    public function getTimeUpdated(): string
    {
        return $this->timeCreated;
    }

    public function getFeedType(): string
    {
        return 'agreement';
    }

    public function getCardBody(): string
    {
        return 'this card is broken';
    }
}