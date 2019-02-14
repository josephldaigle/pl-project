<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/27/18
 * Time: 9:58 AM
 */

namespace PapaLocal\Feed\Message;


use PapaLocal\Core\Service\MessageFactoryInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\User;
use PapaLocal\Feed\Message\Query\ApplyFilter;
use PapaLocal\Feed\Message\Query\LoadFeed;
use PapaLocal\Feed\Message\Query\LoadFeedItem;
use PapaLocal\Feed\ValueObject\FeedFilter;


/**
 * TODO: Integration Test
 *
 * Class MessageFactory
 * @package PapaLocal\Feed\Message
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @param User $user
     * @param array $feedType
     * @return LoadFeed
     */
    public function newLoadFeed(User $user, array $feedType): LoadFeed
    {
        return new LoadFeed($user, $feedType);
    }

    /**
     * @param string $guid
     * @param string $type
     * @return LoadFeedItem
     */
    public function newLoadFeedItem(string $guid, string $type): LoadFeedItem
    {
        return new LoadFeedItem($guid, $type);
    }

    /**
     * @param Collection $feedItems
     * @param FeedFilter $filter
     * @param int $itemCount
     * @return ApplyFilter
     */
    public function newApplyFilter(Collection $feedItems, FeedFilter $filter, int $itemCount): ApplyFilter
    {
        return new ApplyFilter($feedItems, $filter, $itemCount);
    }
}