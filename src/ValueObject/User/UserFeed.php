<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/5/18
 * Time: 9:54 PM
 */

namespace PapaLocal\ValueObject\User;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Collection\FeedList;
use PapaLocal\Entity\FeedItemInterface;


/**
 * Class UserFeed
 *
 * @package PapaLocal\ValueObject\User
 *
 * Model a user's feed.
 */
class UserFeed
{
    /**
     * @var FeedList
     */
    private $feedList;

    /**
     * UserFeed constructor.
     *
     * @param FeedList $feedList
     */
    public function __construct(FeedList $feedList)
    {
        $this->feedList = $feedList;
    }

    /**
     * @param FeedItemInterface $feed
     * @param string            $key
     */
    public function addFeedCard(FeedItemInterface $feed, string $key = null)
    {
        $this->feedList->add($feed, $key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasFeedCard(string $key)
    {
        return $this->feedList->has($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getFeedCard(string $key)
    {
        return $this->feedList->get($key);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function findFeedCardBy($property, $value)
    {
        return $this->feedList->findBy($property, $value);
    }

    /**
     * @return Collection
     */
    public function getFeedList()
    {
        return $this->feedList;
    }

    /**
     * @return array
     */
    public function getAllFeedCards()
    {
        return $this->feedList->all();
    }

    /**
     * @return int
     */
    public function countFeedCards(): int
    {
        return $this->feedList->count();
    }

}