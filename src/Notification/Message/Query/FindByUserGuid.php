<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/12/18
 * Time: 11:24 AM
 */

namespace PapaLocal\Notification\Message\Query;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByUserGuid
 *
 * @package PapaLocal\Notification\Message\Query
 */
class FindByUserGuid
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * FindByUserGuid constructor.
     *
     * @param GuidInterface $userGuid
     */
    public function __construct(GuidInterface $userGuid)
    {
        $this->userGuid = $userGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }
}