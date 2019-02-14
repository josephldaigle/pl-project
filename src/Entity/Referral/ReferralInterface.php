<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/26/18
 */

namespace PapaLocal\Entity\Referral;


/**
 * Interface ReferralInterface.
 *
 * @package PapaLocal\Entity\Referral
 */
interface ReferralInterface
{
    public function getId();
    public function getProvider();
    public function getReferral();
    public function getRecipient();
    public function getDescription();
    public function getDetails();
}