<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/27/18
 * Time: 10:50 AM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class Status
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class Status extends AbstractEnum
{
    private const INACTIVE = 'Inactive';
    private const ACTIVE = 'Active';
}