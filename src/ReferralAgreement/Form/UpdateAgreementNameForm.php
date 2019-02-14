<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 12:27 AM
 */


namespace PapaLocal\ReferralAgreement\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateAgreementName
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class UpdateAgreementNameForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Id cannot be blank."
     *     )
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Name cannot be blank."
     *     )
     */
    private $name;

    /**
     * UpdateAgreementName constructor.
     *
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id = '', string $name = '')
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}