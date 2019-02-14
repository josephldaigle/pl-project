<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 11:06 AM
 */


namespace PapaLocal\Core\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class EmailAddressType
 *
 * @package PapaLocal\Core\ValueObject
 */
class EmailAddressType extends AbstractEnum
{
    private const USERNAME   = 'Username';
    private const BUSINESS   = 'Business';
    private const PERSONAL   = 'Personal';
    private const OTHER      = 'Other';
}