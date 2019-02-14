<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */


namespace PapaLocal\ReferralAgreement\Form\Agreement;



use Symfony\Component\Validator\Constraints as Assert;



/**
 * Class UpdateLocationsForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateLocationsForm
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
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "You must provide an included location."
     *     )
     */
    private $locations;

    /**
     * @var string
     */
    private $context;

    /**
     * UpdateLocationsForm constructor.
     *
     * @param string $agreementGuid
     * @param array  $locations
     * @param string $context
     */
    public function __construct(string $agreementGuid = '', array $locations = [], $context = '')
    {
        $this->agreementGuid = $agreementGuid;
        $this->locations     = $locations;
        $this->context       = $context;
    }


    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid;
    }

    /**
     * @return array
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext(string $context)
    {
        $this->context = $context;
    }
}