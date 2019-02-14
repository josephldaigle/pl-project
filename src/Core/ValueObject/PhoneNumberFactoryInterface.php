<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/16/18
 */

namespace PapaLocal\Core\ValueObject;

/**
 * Class PhoneNumberFactoryInterface.
 *
 * Describe a factory for creating PhoneNumber value objects.
 *
 * @package PapaLocal\Core\ValueObject
 */
interface PhoneNumberFactoryInterface
{
    /**
     * Create a PhoneNumber object.
     *
     * @param string          $phoneNumber
     * @param PhoneNumberType $phoneNumberType
     *
     * @return PhoneNumber
     */
    public function createPhoneNumber(string $phoneNumber, PhoneNumberType $phoneNumberType): PhoneNumber;
}