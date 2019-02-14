<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 9:41 AM
 */


namespace PapaLocal\Core\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class PhoneNumberType
 *
 * @package PapaLocal\Core\ValueObject
 */
class PhoneNumberType extends AbstractEnum
{
    private const BUSINESS   = 'Business';
    private const PERSONAL = 'Personal';
    private const MAIN   = 'Main';
    private const CELL = 'Cell';
    private const FAX = 'Fax';
    private const OTHER = 'Other';
}