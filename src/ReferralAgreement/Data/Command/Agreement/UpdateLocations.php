<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:12 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;


/**
 * Class UpdateAgreementLocations
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateLocations
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var array
     */
    private $locations;

    /**
     * UpdateLocations constructor.
     *
     * @param GuidInterface      $agreementGuid
     * @param IncludeExcludeList $locations
     */
    public function __construct(GuidInterface $agreementGuid, IncludeExcludeList $locations)
    {
        $this->agreementGuid = $agreementGuid;

        $this->setLocations($locations->all());
    }


    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * @param array $locations
     *
     * @throws \InvalidArgumentException
     */
    private function setLocations(array $locations)
    {
        if (count($locations) < 1) {
            throw new \InvalidArgumentException(sprintf('Param 2 provided to %s cannot be empty.', __METHOD__));
        }

        foreach ($locations as $location) {
            if (! $location instanceof Location) {
                throw new \InvalidArgumentException(sprintf('All elements provided in param 2 to %s::__construct() must be instances of %s.', __CLASS__, Location::class));
            }

            $this->locations[] = array(
                'agreementId' => '',
                'location' => $location->getLocation(),
                'type' => $location->getType()->getValue()
            );
        }
    }

    /**
     * @return array
     */
    public function getLocations(): array
    {
        return $this->locations;
    }
}