<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/16/18
 */

namespace PapaLocal\Core\ValueObject;

/**
 * Class EmailAddressFactoryInterface.
 *
 * Describe an email address.
 *
 * @package PapaLocal\Core\ValueObject
 */
interface  EmailAddressFactoryInterface
{
    /**
     * @param string           $emailAddress
     * @param EmailAddressType $emailAddressType
     *
     * @return EmailAddress
     */
    public function createEmailAddress(string $emailAddress, EmailAddressType $emailAddressType): EmailAddress;
}