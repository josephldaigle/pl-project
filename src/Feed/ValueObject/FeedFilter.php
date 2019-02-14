<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/23/18
 * Time: 8:41 PM
 */


namespace PapaLocal\Feed\ValueObject;


use PapaLocal\Feed\Entity\FeedFilterInterface;


/**
 * Class FeedFilter
 *
 * @package PapaLocal\ValueObject
 *
 * Models the feed filter.
 */
class FeedFilter implements FeedFilterInterface
{
    /**
	 * @var array
	 */
	private $types;

	/**
	 * @var string
	 */
	private $startDate;

	/**
	 * @var string
	 */
	private $endDate;

	/**
	 * @var string
	 */
	private $sortOrder;

    /**
     * FeedFilter constructor.
     * @param array $types
     * @param string $startDate
     * @param string $endDate
     * @param string $sortOrder
     */
    public function __construct(array $types, string $startDate = null, string $endDate = null, $sortOrder = null)
    {
        $this->setTypes($types);
        $this->startDate = $this->setStartDate($startDate);
        $this->endDate   = $this->setEndDate($endDate);
        $this->sortOrder = $this->setSortOrder($sortOrder);
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes(array $types)
    {
        $this->types = ($types == array('all') || $types == array('transaction', 'agreement', 'referral', 'notification')) ? array('transaction', 'agreement', 'referral', 'notification') : $types;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     * @return mixed
     */
    public function setStartDate($startDate)
    {
        $value = ($startDate == '' || $startDate == null ? '01/01/2015' : $startDate);
        return $value;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     * @return mixed
     */
    public function setEndDate($endDate)
    {
        $value = ($endDate == '' || $endDate == null  ? date("m/d/Y") : $endDate);
        return $value;
    }

    /**
     * @return string
     */
    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     * @return mixed
     */
    public function setSortOrder($sortOrder)
    {
        $value = ($sortOrder == '' || $sortOrder == null ? 'NEWEST_FIRST' : $sortOrder);
        return $value;
    }
}