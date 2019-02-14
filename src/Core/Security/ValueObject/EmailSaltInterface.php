<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/9/18
 */


namespace PapaLocal\Core\Security\ValueObject;


use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;


/**
 * Interface EmailSaltInterface.
 *
 * @package PapaLocal\Core\Security\ValueObject
 */
interface EmailSaltInterface
{
    /**
     * @return Guid
     */
    public function getId(): Guid;

    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @return EmailSaltPurpose
     */
    public function getPurpose(): EmailSaltPurpose;

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress;

    /**
     * @return \DateInterval
     */
    public function getExpirationPolicy(): \DateInterval;
}