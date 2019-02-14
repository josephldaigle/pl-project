<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/16/18
 */

namespace PapaLocal\Core\Factory;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressFactoryInterface;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberFactoryInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;


/**
 * Class VOFactory.
 *
 * @package PapaLocal\Core\Factory
 */
class VOFactory implements EmailAddressFactoryInterface, PhoneNumberFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string           $emailAddress
     * @param EmailAddressType $emailAddressType
     *
     * @return EmailAddress
     */
    public function createEmailAddress(string $emailAddress, EmailAddressType $emailAddressType): EmailAddress
    {
        return new EmailAddress($emailAddress, $emailAddressType);
    }

    /**
     * {@inheritdoc}
     *
     * @param string          $phoneNumber
     * @param PhoneNumberType $phoneNumberType
     *
     * @return PhoneNumber
     */
    public function createPhoneNumber(string $phoneNumber, PhoneNumberType $phoneNumberType): PhoneNumber
    {
        return new PhoneNumber($phoneNumber, $phoneNumberType);
    }


}