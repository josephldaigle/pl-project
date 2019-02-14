<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:37 AM
 */


namespace PapaLocal\ReferralAgreement\Form\Agreement;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateDescriptionForm
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateDescriptionForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Agreement id cannot be blank."
     *     )
     */
    private $agreementGuid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Description cannot be blank."
     *     )
     *
     */
    private $description;

    /**
     * UpdateDescriptionForm constructor.
     *
     * @param string $agreementGuid
     * @param string $description
     */
    public function __construct(string $agreementGuid = '', string $description = '')
    {
        $this->agreementGuid = $agreementGuid;
        $this->description   = $description;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}