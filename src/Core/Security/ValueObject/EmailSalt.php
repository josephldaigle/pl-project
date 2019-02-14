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

use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class EmailSalt.
 *
 * @package PapaLocal\Core\ValueObject\Security
 *
 * Model an EmailSalt.
 */
class EmailSalt implements EmailSaltInterface
{
    /**
     * @Assert\NotBlank(
     *        message = "Salt cannot be blank."
     * )
     *
     * @var Guid
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *        message = "Hash cannot be blank."
     * )
     */
    private $hash;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var EmailSaltPurpose
     */
    private $purpose;

    /**
     * @var \DateInterval
     */
    private $expirationPolicy;

    /**
     * EmailSalt constructor.
     *
     * @param Guid             $id
     * @param string           $hash
     * @param EmailAddress     $emailAddress
     * @param EmailSaltPurpose $purpose
     * @param \DateInterval    $expirationPolicy
     */
    public function __construct(
        Guid $id,
        string $hash,
        EmailAddress $emailAddress,
        EmailSaltPurpose $purpose,
        \DateInterval $expirationPolicy
    )
    {
        $this->id               = $id;
        $this->hash             = $hash;
        $this->emailAddress     = $emailAddress;
        $this->purpose          = $purpose;
        $this->expirationPolicy = $expirationPolicy;
    }


    /**
     * @return Guid
     */
    public function getId(): Guid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * @return EmailSaltPurpose
     */
    public function getPurpose(): EmailSaltPurpose
    {
        return $this->purpose;
    }

    /**
     * @return \DateInterval
     */
    public function getExpirationPolicy(): \DateInterval
    {
        return $this->expirationPolicy;
    }
}