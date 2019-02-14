<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class UpdateStrategyHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateStrategyHandler
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var ReferralAgreementService
     */
    private $agreementService;

    /**
     * UpdateStrategyHandler constructor.
     *
     * @param GuidGeneratorInterface   $guidFactory
     * @param ReferralAgreementService $agreementService
     */
    public function __construct(GuidGeneratorInterface $guidFactory, ReferralAgreementService $agreementService)
    {
        $this->guidFactory = $guidFactory;
        $this->agreementService = $agreementService;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateStrategy $command)
    {
        $agmtGuid = $this->guidFactory->createFromString($command->getAgreementGuid());
        $this->agreementService->updateStrategy($agmtGuid, $command->getStrategy());

        return;
    }


}