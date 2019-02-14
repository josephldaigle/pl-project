<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/9/18
 */


namespace PapaLocal\Core\Security\Entity;


use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;


/**
 * Class EmailSalt.
 *
 * @package PapaLocal\Core\Entity\Security
 *
 * Model a saved email salt.
 */
class EmailSalt
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var EmailSaltPurpose
     */
    private $purpose;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @var \DateInterval
     */
    private $expirationPolicy;

    /**
     * @return Guid
     */
    public function getId(): Guid
    {
        return $this->id;
    }

    /**
     * @param Guid $id
     *
     * @return EmailSalt
     */
    public function setId(Guid $id): EmailSalt
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * @param EmailAddress $emailAddress
     *
     * @return EmailSalt
     */
    public function setEmailAddress(EmailAddress $emailAddress): EmailSalt
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return EmailSalt
     */
    public function setHash(string $hash): EmailSalt
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return EmailSaltPurpose
     */
    public function getPurpose(): EmailSaltPurpose
    {
        return $this->purpose;
    }

    /**
     * @param EmailSaltPurpose $purpose
     *
     * @return EmailSalt
     */
    public function setPurpose(EmailSaltPurpose $purpose): EmailSalt
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     *
     * @return EmailSalt
     */
    public function setTimeCreated(string $timeCreated): EmailSalt
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /**
     * @return \DateInterval
     */
    public function getExpirationPolicy(): \DateInterval
    {
        return $this->expirationPolicy;
    }

    /**
     * @param \DateInterval $expirationPolicy
     *
     * @return EmailSalt
     */
    public function setExpirationPolicy(\DateInterval $expirationPolicy): EmailSalt
    {
        $this->expirationPolicy = $expirationPolicy;

        return $this;
    }

    /**
     * Whether or not this salt is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        $created = new \DateTime($this->getTimeCreated(), new \DateTimeZone('UTC'));
        $sinceCreated = $created->diff(new \DateTime('now', new \DateTimeZone('America/New_York')));

        $minutes = $sinceCreated->days * 24 * 60;
        $minutes += $sinceCreated->h * 60;
        $minutes += $sinceCreated->i;

        return $minutes > 30;
    }
}