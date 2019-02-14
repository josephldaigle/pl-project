<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/27/18
 * Time: 7:20 PM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Class Service
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class Service
{
    /**
     * @var Guid
     */
    private $agreementId;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Service name cannot be blank."
     *     )
     *
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Your service name cannot be longer than {{ limit }} characters."
     * )
     */
    private $service;

    /**
     * @var ServiceType
     *
     * Assert\NotBlank(
     *     message = "Service type cannot be blank."
     *     )
     */
    private $type;

    /**
     * Service constructor.
     *
     * @param Guid        $agreementId
     * @param string      $service
     * @param ServiceType $type
     */
    public function __construct(string $service,
                                ServiceType $type,
                                Guid $agreementId = null)
    {
        $this->setService($service);
        $this->setType($type);

        if (! is_null($agreementId)) {
            $this->setAgreementId($agreementId);
        }
    }

    /**
     * @param Guid $agreementId
     */
    protected function setAgreementId(Guid $agreementId)
    {
        $this->agreementId = $agreementId;
    }

    /**
     * @param string $service
     */
    protected function setService(string $service)
    {
        $this->service = $service;
    }

    /**
     * @param ServiceType $type
     */
    protected function setType(ServiceType $type)
    {
        $this->type = $type;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementId(): GuidInterface
    {
        return $this->agreementId;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return ServiceType
     */
    public function getType(): ServiceType
    {
        return $this->type;
    }
}