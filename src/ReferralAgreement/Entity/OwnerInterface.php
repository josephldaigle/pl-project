<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/17/18
 */

namespace PapaLocal\ReferralAgreement\Entity;


use PapaLocal\Entity\PersonInterface;
use PapaLocal\Entity\UserInterface;


/**
 * Class OwnerInterface.
 *
 * @package PapaLocal\ReferralAgreement\Entity
 *
 * Describe a Referral Agreement Owner
 */
interface OwnerInterface extends PersonInterface, UserInterface
{
}