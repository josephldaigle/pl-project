<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:02 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByUserGuid
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
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