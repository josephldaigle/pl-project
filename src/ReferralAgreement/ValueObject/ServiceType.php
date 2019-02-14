<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:35 PM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class ServiceType
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class ServiceType extends AbstractEnum
{
    private const INCLUDE = 'include';
    private const EXCLUDE = 'exclude';
}