<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 8:38 PM
 */

namespace PapaLocal\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * PhoneNumber.
 *
 * Model a telephone number in the US and Canada.
 */
class PhoneNumber extends Entity
{
    /**
     * @var int
     *
     *  @Assert\Blank(
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
     * @Assert\Type(
     *     type="numeric",
     *     message="The phone number must be only numbers.",
     *     groups = {"create", "update", "form_submit"}
     * )
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "The phone number must be at exactly {{ limit }} digits long.",
     *      groups = {"create", "update", "form_submit"}
     * )
     */
    private $phoneNumber;

    /**
     * @var string
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return PhoneNumber
     */
    public function setId(int $id): PhoneNumber
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     *
     * @return PhoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): PhoneNumber
    {
        $this->phoneNumber = $phoneNumber;
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
     * @return PhoneNumber
     */
    public function setType(string $type): PhoneNumber
    {
        $this->type = $type;
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
     * @return PhoneNumber
     */
    public function setTimeCreated(string $timeCreated): PhoneNumber
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * @param string $timeUpdated
     *
     * @return PhoneNumber
     */
    public function setTimeUpdated(string $timeUpdated): PhoneNumber
    {
        $this->timeUpdated = $timeUpdated;
        return $this;
    }

}