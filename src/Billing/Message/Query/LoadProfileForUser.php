<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 5:12 PM
 */


namespace PapaLocal\Billing\Message\Query;


use PapaLocal\Core\Messenger\Query\AppQueryInterface;
use PapaLocal\Core\ValueObject\Guid;


/**
 * Class LoadProfileForUser
 *
 * @package PapaLocal\Billing\Message\Query
 */
class LoadProfileForUser
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * LoadProfileForUser constructor.
     *
     * @param string $userGuid
     */
    public function __construct(string $userGuid)
    {
        $this->userGuid = $userGuid;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }
}