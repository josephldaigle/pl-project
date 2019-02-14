<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 3:27 PM
 */


namespace PapaLocal\Core\ValueObject;


/**
 * Interface EmailAddressInterface
 *
 * @package PapaLocal\Core\ValueObject
 */
interface EmailAddressInterface
{
    /**
     * @return string
     */
    public function getEmailAddress(): string;
}