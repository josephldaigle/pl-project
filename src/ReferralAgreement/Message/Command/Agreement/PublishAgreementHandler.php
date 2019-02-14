<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/18
 * Time: 1:26 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class PublishAgreementHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command
 */
class PublishAgreementHandler
{
    /**
     * @var ReferralAgreementService
     */
    private $referralAgreementService;

    /**
     * PublishAgreementHandler constructor.
     *
     * @param ReferralAgreementService $referralAgreementService
     */
    public function __construct(
        ReferralAgreementService $referralAgreementService
    )
    {
        $this->referralAgreementService = $referralAgreementService;
    }


    /**
     * @param PublishAgreement $command
     *
     * @throws \PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException
     */
    function __invoke(PublishAgreement $command)
    {
        // invoke publish workflow
        $this->referralAgreementService->publishAgreement($command->getAgreementGuid());

        return;
    }
}