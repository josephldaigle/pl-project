<?php

/**
 * Created by PhpStorm.
 * Date: 10/12/18
 * Time: 7:52 AM
 */

namespace PapaLocal\Referral\Message\Command;


use PapaLocal\Referral\Form\ReferralRate;


/**
 * Class RateReferral
 * @package PapaLocal\Referral\Message\Command
 */
class RateReferral
{
    /**
    * @var ReferralRate
    */
    private $referralRate;

    /**
     * RateReferral constructor.
     * @param ReferralRate $referralRate
     */
    public function __construct(ReferralRate $referralRate)
    {
        $this->referralRate = $referralRate;
    }

    /**
     * @return ReferralRate
     */
    public function getReferralRate(): ReferralRate
    {
        return $this->referralRate;
    }
}