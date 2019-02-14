<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/17/18
 * Time: 3:08 PM
 */

namespace PapaLocal\Referral\Data;


use PapaLocal\Core\Data\AbstractMessageFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Data\Command\SaveReferral;
use PapaLocal\Referral\Data\Command\UpdateReferral;
use PapaLocal\Referral\Data\Query\FetchAllReferrals;
use PapaLocal\Referral\Entity\Referral;


/**
 * Class MessageFactory
 * @package PapaLocal\Referral\Data
 */
class MessageFactory extends AbstractMessageFactory
{
    /**
     * @param Referral $referral
     * @param string $currentPlace
     * @return SaveReferral
     */
    public function newSaveReferral(Referral $referral, string $currentPlace): SaveReferral
    {
        return new SaveReferral($referral, $currentPlace);
    }

    /**
     * @param Referral $referral
     * @param string $currentPlace
     * @return UpdateReferral
     */
    public function newUpdateReferral(Referral $referral, string $currentPlace): UpdateReferral
    {
        return new UpdateReferral($referral, $currentPlace);
    }
}