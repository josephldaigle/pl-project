<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/29/18
 * Time: 11:36 AM
 */

namespace PapaLocal\Feed\Message\Query;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Feed\ValueObject\FeedFilter;


/**
 * Class ApplyFilter
 * @package PapaLocal\Feed\Message\Query
 */
class ApplyFilter
{
    /**
     * @var Collection
     */
    private $feedItems;

    /**
     * @var FeedFilter
     */
    private $feedFilter;

    /**
     * @var int
     */
    private $count;

    /**
     * ApplyFilter constructor.
     * @param Collection $feedItems
     * @param FeedFilter $feedFilter
     * @param int $count
     */
    public function __construct(Collection $feedItems, FeedFilter $feedFilter, int $count)
    {
        $this->feedItems = $feedItems;
        $this->feedFilter = $feedFilter;
        $this->count = $count;
    }

    /**
     * @return Collection
     */
    public function getFeedItems(): Collection
    {
        return $this->feedItems;
    }

    /**
     * @return FeedFilter
     */
    public function getFeedFilter(): FeedFilter
    {
        return $this->feedFilter;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

}