<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 9:41 AM
 */


namespace PapaLocal\Core\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class PhoneNumber
 *
 * @package PapaLocal\Core\ValueObject
 */
class PhoneNumber
{
    /**
     * @var string
     *
     * @Assert\Type(
     *     type = "numeric",
     *     message = "The phone number can only contain numbers."
     * )
     *
     * @Assert\Regex(
     *     pattern = "/^[1-9]/",
     *     match = true,
     *     message = "The phone number cannot begin with zero."
     * )
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "The phone number must be exactly {{ limit }} digits long."
     * )
     */
    private $phoneNumber;

    /**
     * @var PhoneNumberType
     */
    private $type;

    /**
     * PhoneNumber constructor.
     *
     * @param string          $phoneNumber
     * @param PhoneNumberType $type
     */
    public function __construct(string $phoneNumber, PhoneNumberType $type)
    {
        $this->setPhoneNumber($phoneNumber);
        $this->setType($type);
    }

    /**
     * @param string $phoneNumber
     *
     * @return PhoneNumber
     */
    protected function setPhoneNumber(string $phoneNumber): PhoneNumber
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @param PhoneNumberType $type
     *
     * @return PhoneNumber
     */
    protected function setType(PhoneNumberType $type): PhoneNumber
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return PhoneNumberType
     */
    public function getType(): PhoneNumberType
    {
        return $this->type;
    }
}