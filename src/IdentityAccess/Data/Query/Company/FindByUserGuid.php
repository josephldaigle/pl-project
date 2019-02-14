<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 9:00 PM
 */

namespace PapaLocal\IdentityAccess\Data\Query\Company;


/**
 * Class FindByUserGuid
 *
 * @package PapaLocal\IdentityAccess\Data\Query\Company
 */
class FindByUserGuid
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * FindByUserId constructor.
     *
     * @param string $userGuid
     */
    public function __construct($userGuid)
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