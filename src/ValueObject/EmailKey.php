<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/8/18
 * Time: 8:20 PM
 */

namespace PapaLocal\ValueObject;


use Symfony\Component\Serializer\Annotation as Serialize;


/**
 * Class EmailKey.
 *
 * @package PapaLocal\ValueObject
 */
class EmailKey
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $personId;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var int
     */
    private $salt;

    /**
     * @var
     */
    private $purpose;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return EmailKey
     */
    public function setId(int $id): EmailKey
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     *
     * @return EmailKey
     */
    public function setEmailAddress(string $emailAddress): EmailKey
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return int
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param int $salt
     *
     * @return EmailKey
     */
    public function setSalt(int $salt): EmailKey
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     *
     * @return EmailKey
     */
    public function setTimeCreated(string $timeCreated): EmailKey
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    /**
     * Whether or not this key is expired.
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