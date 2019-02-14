<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/21/17
 * Time: 6:50 PM
 */

namespace PapaLocal\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EmailAddress.
 *
 * @package PapaLocal\Entity
 */
class EmailAddress extends Entity
{
    /**
     * @var int
     *
     * @Assert\Blank(
     *     message = "Id must be blank.",
     *     groups = {"create"}
     *     )
     *
     * @Assert\NotBlank(
     *     message = "Id must be present.",
     *     groups = {"update"}
     *     )
     *
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "The email address is invalid.",
     *     groups={"create", "update", "form_submit"}
     *     )
     *
     * @Assert\NotBlank(
     *     message = "Email cannot be blank.",
     *     groups = {"create", "form_submit"}
     *     )
     *
     * @Assert\Length(
     *     max = 36,
     *     maxMessage = "Your email address cannot be longer than {{ limit }} characters.",
     *     groups = {"create", "form_submit", "update"}
     * )
     */
    private $emailAddress;

    /**
     * @var string  the type of email address
     *
     * @Assert\NotBlank(
     *     message = "Type must be present.",
     *     groups = {"create"}
     * )
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time created must be blank.",
     *     groups = {"create", "update"}
     *     )
     */
    private $timeCreated;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time updated must be blank.",
     *     groups = {"create", "update"}
     *     )
     */
    private $timeUpdated;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return EmailAddress
     */
    public function setId($id): EmailAddress
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $emailAddress
     *
     * @return EmailAddress
     */
    public function setEmailAddress($emailAddress): EmailAddress
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param mixed $timeCreated
     *
     * @return EmailAddress
     */
    public function setTimeCreated($timeCreated): EmailAddress
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * @param mixed $timeUpdated
     *
     * @return EmailAddress
     */
    public function setTimeUpdated($timeUpdated): EmailAddress
    {
        $this->timeUpdated = $timeUpdated;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return EmailAddress
     */
    public function setType(string $type): EmailAddress
    {
        $this->type = $type;
        return $this;
    }
}