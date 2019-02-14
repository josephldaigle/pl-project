<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:52 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\User;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindUserByGuid
 *
 * @package PapaLocal\IdentityAccess\Message\Query\User
 */
class FindUserByGuid
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * FindUserByGuid constructor.
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