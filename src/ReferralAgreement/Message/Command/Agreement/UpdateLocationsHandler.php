<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateLocationsForm;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\LocationType;


/**
 * Class UpdateLocationsHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateLocationsHandler
{
    /**
     * @var GuidFactory
     */
    private $guidFactory;

    /**
     * @var ReferralAgreementService
     */
    private $agreementService;

    /**
     * UpdateLocationsHandler constructor.
     *
     * @param GuidFactory              $guidFactory
     * @param ReferralAgreementService $agreementService
     */
    public function __construct(GuidFactory $guidFactory, ReferralAgreementService $agreementService)
    {
        $this->guidFactory = $guidFactory;
        $this->agreementService = $agreementService;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateLocationsForm $command)
    {
        $agreementGuid = $this->guidFactory->createFromString($command->getAgreementGuid());

        $includeExcludeList = new IncludeExcludeList();
        foreach($command->getLocations() as $locValue) {
            $location = new Location($locValue, LocationType::{strtoupper($command->getContext())}());
            $includeExcludeList->add($location);
        }

        $this->agreementService->updateLocations($agreementGuid, $includeExcludeList);

        return;
    }


}