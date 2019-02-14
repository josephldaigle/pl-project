<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/3/18
 * Time: 9:51 AM
 */

namespace PapaLocal\Feed\ValueObject;


use PapaLocal\Entity\FeedItemInterface;


/**
 * Class SortBy
 * @package PapaLocal\Feed\ValueObject
 */
class SortBy
{
    public const NEWEST_FIRST = 'createdDescending';
    public const OLDEST_FIRST = 'createdAscending';
    public const ALPHABETICAL = 'titleAscending';
    public const LAST_UPDATED = 'updatedDescending';

    /**
     * @param FeedItemInterface $a
     * @param FeedItemInterface $b
     * @return int
     */
    public static function titleAscending(FeedItemInterface $a, FeedItemInterface $b)
    {
        return strcmp($a->getTitle(), $b->getTitle());
    }

    /**
     * @param FeedItemInterface $a
     * @param FeedItemInterface $b
     * @return false|int
     */
    public static function updatedDescending(FeedItemInterface $a, FeedItemInterface $b)
    {
        return strtotime($b->getTimeUpdated()) - strtotime($a->getTimeUpdated());
    }

    /**
     * @param FeedItemInterface $a
     * @param FeedItemInterface $b
     * @return false|int
     */
    public static function createdDescending(FeedItemInterface $a, FeedItemInterface $b)
    {
        return strtotime($b->getTimeCreated()) - strtotime($a->getTimeCreated());
    }

    /**
     * @param FeedItemInterface $a
     * @param FeedItemInterface $b
     * @return false|int
     */
    public static function createdAscending(FeedItemInterface $a, FeedItemInterface $b)
    {
        return strtotime($a->getTimeCreated()) - strtotime($b->getTimeCreated());
    }
}