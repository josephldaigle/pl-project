<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/6/18
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class Strategy.
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class Strategy extends AbstractEnum
{
    private const WEEKLY = 'Weekly';
    private const MONTHLY = 'Monthly';
}