<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:22 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Service;


/**
 * Class UpdateServices
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateServices
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var array
     */
    private $services;

    /**
     * UpdateServices constructor.
     *
     * @param GuidInterface      $agreementGuid
     * @param IncludeExcludeList $services
     */
    public function __construct(GuidInterface $agreementGuid, IncludeExcludeList $services)
    {
        $this->agreementGuid = $agreementGuid;

        $this->setServices($services->all());
    }


    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * Set services as an array of arrays, instead of collection.
     *
     * @param array $services
     *
     * @throws \InvalidArgumentException
     */
    private function setServices(array $services)
    {
        if (count($services) < 1) {
            throw new \InvalidArgumentException(sprintf('Param 2 provided to %s cannot be empty.', __METHOD__));
        }

        foreach ($services as $service) {
            if ( ! $service instanceof Service) {
                throw new \InvalidArgumentException(sprintf('All elements provided in param 2 to %s::__construct() must be instances of %s.',
                    __CLASS__, Service::class));
            }

            $this->services[] = array(
                'agreementId' => '',
                'service'     => $service->getService(),
                'type'        => $service->getType()->getValue()
            );
        }
    }

    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }
}