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
 * Class UpdateQuantityHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateQuantityHandler
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
     * UpdateQuantityHandler constructor.
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
    public function __invoke(UpdateQuantity $command)
    {
        // create objects

        $agmtGuid = $this->guidFactory->createFromString($command->getAgreementGuid());
        $this->agreementService->updateQuantity($agmtGuid, $command->getQuantity());

        return;
    }

}