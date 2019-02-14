<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:42 AM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class UpdateDescriptionHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateDescriptionHandler
{
    /**
     * @var ReferralAgreementService
     */
    private $referralAgreementService;

    /**
     * UpdateDescriptionHandler constructor.
     *
     * @param ReferralAgreementService $referralAgreementService
     */
    public function __construct(ReferralAgreementService $referralAgreementService)
    {
        $this->referralAgreementService = $referralAgreementService;
    }

    /**
     * @param UpdateName $command
     */
    function __invoke(UpdateDescription $command)
    {
        $this->referralAgreementService->updateAgreementDescription($command->getAgreementGuid(), $command->getAgreementDescription());
        return;
    }

}