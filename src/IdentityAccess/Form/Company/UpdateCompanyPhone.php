<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 6:03 PM
 */


namespace PapaLocal\IdentityAccess\Form\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateCompanyPhone
 *
 * @package PapaLocal\IdentityAccess\Form\Company
 */
class UpdateCompanyPhone
{
    /**
     * @var string
     */
    private $guid;

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
     *
     * @Assert\NotNull(
     *     message = "Phone number must be present."
     * )
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $type;

    /**
     * UpdateCompanyPhone constructor.
     *
     * @param string $guid
     * @param string $phoneNumber
     * @param string $type
     */
    public function __construct(string $guid, string $phoneNumber = null, string $type)
    {
        $this->guid        = $guid;
        $this->phoneNumber = $phoneNumber;
        $this->type        = $type;
    }

    /**
     * @return string
     */
    public function getCompanyGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

}