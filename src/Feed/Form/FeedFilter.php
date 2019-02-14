<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 3/1/18
 * Time: 4:25 PM
 */

namespace PapaLocal\Feed\Form;


use PapaLocal\Feed\Entity\FeedFilterInterface;
use PapaLocal\Core\Validation\BeforeNow;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class FeedFilter
 *
 * @package PapaLocal\ValueObject\Form
 *
 * Model the form that user uses to filter the feed.
 */
class FeedFilter implements FeedFilterInterface
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "You must select one of the type icons."
     * )
     *
     * @Assert\Choice(choices={
     *     {"all"},
     *     {"notification"},
     *     {"transaction"},
     *     {"transaction","agreement"},
     *     {"transaction","referral"},
     *     {"transaction","agreement","referral"},
     *     {"transaction","agreement","referral", "notification"},
     *     {"agreement"},
     *     {"agreement","referral"},
     *     {"referral"}
     *     }, message = "Invalid type selected."
     * )
     *
     */
    private $types;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *    message = "A start date must be selected."
     * )
     *
     * @BeforeNow(
     *    message = "The start date cannot be after the current time."
     * )
     */
    private $startDate;

    /**
     * @var string
     *
     * @Assert\Expression(
     *     "this.validateEndDate()",
     *     message = "The start date must be earlier than end date."
     * )
     */
    private $endDate;

    /**
     * @var string
     *
     * @Assert\Choice(
     *     choices = {"NEWEST_FIRST", "OLDEST_FIRST", "ALPHABETICAL", "LAST_UPDATED"},
     *     message = "Invalid sort order.",
     *     strict = true
     * )
     *
     */
    private $sortOrder;

    /**
     * @var int the number of records to retrieve
     */
    private $fetchCount;

    /**
     * @var int the starting index of the items to retrieve
     */
    private $beginWith = 0;

	/**
	 * @var SelectFeedItemForm
	 */
    private $selectedItem;

    /**
     * FeedFilter constructor.
     */
    public function __construct()
    {
        $this->fetchCount = $this->beginWith + 15;
    }


    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     * @return FeedFilter
     */
    public function setTypes(array $types): FeedFilter
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     * @return FeedFilter
     */
    public function setStartDate(string $startDate): FeedFilter
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     * @return FeedFilter
     */
    public function setEndDate(string $endDate): FeedFilter
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     * @return FeedFilter
     */
    public function setSortOrder(string $sortOrder): FeedFilter
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSelectedItem()
    {
        return $this->selectedItem;
    }

    /**
     * @param SelectFeedItemForm $selectedItem
     *
     * @return FeedFilter
     */
    public function setSelectedItem(SelectFeedItemForm $selectedItem): FeedFilter
    {
        $this->selectedItem = $selectedItem;
        return $this;
    }

    /**
     * @return int
     */
    public function getFetchCount(): int
    {
        return $this->fetchCount;
    }

    /**
     * The starting index after all other filters are applied.
     *
     * @return int
     */
    public function getBeginWith(): int
    {
        return $this->beginWith;
    }

    /**
     * @return bool
     */
    public function validateEndDate()
    {
        $start = strtotime($this->startDate);
        $end = strtotime($this->endDate);

        return ($start <= $end);
    }
}