<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 6:59 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\Company;


use PapaLocal\Core\ValueObject\GuidInterface;

/**
 * Class FindByUserGuid
 *
 * @package PapaLocal\IdentityAccess\Message\Query\Company
 */
class FindByUserGuid
{
    /**
     * @var GuidInterface
     */
    private $userId;

    /**
     * FindByUserId constructor.
     *
     * @param GuidInterface $userId
     */
    public function __construct(GuidInterface $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return GuidInterface
     */
    public function getUserId(): GuidInterface
    {
        return $this->userId;
    }
}