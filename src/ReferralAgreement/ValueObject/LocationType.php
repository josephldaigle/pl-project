<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:59 PM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class LocationType
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class LocationType extends AbstractEnum
{
    private const INCLUDE = 'include';
    private const EXCLUDE = 'exclude';
}