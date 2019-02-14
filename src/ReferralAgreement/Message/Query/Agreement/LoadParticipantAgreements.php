<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/29/18
 * Time: 12:13 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class LoadParticipantAgreements
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadParticipantAgreements
{
    /**
     * @var GuidInterface
     */
    private $participantUserGuid;

    /**
     * LoadParticipantAgreements constructor.
     *
     * @param GuidInterface $participantUserGuid
     */
    public function __construct(GuidInterface $participantUserGuid)
    {
        $this->participantUserGuid = $participantUserGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getParticipantUserGuid(): GuidInterface
    {
        return $this->participantUserGuid;
    }
}