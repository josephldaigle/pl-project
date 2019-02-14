<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 9:55 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class LoadUserAgreements
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadUserAgreements
{
    /**
     * @var GuidInterface the owner of the agreements to load
     */
    private $ownerGuid;

    /**
     * LoadUserAgreements constructor.
     *
     * @param GuidInterface $userGuid
     */
    public function __construct(GuidInterface $userGuid)
    {
        $this->ownerGuid = $userGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getOwnerGuid(): GuidInterface
    {
        return $this->ownerGuid;
    }
}