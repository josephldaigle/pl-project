<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/27/18
 * Time: 10:00 AM
 */

namespace PapaLocal\Feed\Message\Query;


use PapaLocal\Entity\User;


/**
 * Class LoadFeed
 * @package PapaLocal\Feed\Message\Query
 */
class LoadFeed
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $feedType;

    /**
     * LoadFeed constructor.
     * @param User $user
     * @param array $feedType
     */
    public function __construct(User $user, array $feedType)
    {
        $this->user = $user;
        $this->feedType = $feedType;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getFeedType(): array
    {
        return $this->feedType;
    }
}