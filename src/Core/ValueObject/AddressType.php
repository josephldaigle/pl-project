<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\Core\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class AddressType.
 *
 * @package PapaLocal\Core\ValueObject
 */
class AddressType extends AbstractEnum
{
    private const MAILING   = 'Mailing';
    private const BILLING = 'Billing';
    private const SHIPPING   = 'Shipping';
    private const PHYSICAL = 'Physical';
}