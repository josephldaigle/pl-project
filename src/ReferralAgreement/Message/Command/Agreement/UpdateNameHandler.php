<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 1:00 AM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class UpdateNameHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateNameHandler
{
    /**
     * @var ReferralAgreementService
     */
    private $referralAgreementService;

    /**
     * UpdateNameHandler constructor.
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
    function __invoke(UpdateName $command)
    {
        $this->referralAgreementService->updateReferralAgreementName($command->getAgreementGuid(), $command->getName());
        return;
    }


}