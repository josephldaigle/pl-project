<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateReferralPriceForm;
use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class UpdateReferralPriceHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateReferralPriceHandler
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
     * UpdateReferralPriceHandler constructor.
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
    public function __invoke(UpdateReferralPriceForm $form)
    {
        $agreementGuid = $this->guidFactory->createFromString($form->getAgreementGuid());
        $this->agreementService->updateReferralPrice($agreementGuid, $form->getPrice());
        return;
    }


}