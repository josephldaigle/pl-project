<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/18
 * Time: 8:58 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;


/**
 * Class LoadInviteeAgreementsHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadInviteeAgreementsHandler
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * LoadInviteeAgreementsHandler constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     */
    public function __construct(ReferralAgreementRepository $referralAgreementRepository)
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
    }

    /**
     * @param LoadInviteeAgreements $query
     *
     * @return mixed
     */
    public function __invoke(LoadInviteeAgreements $query)
    {
        return $this->referralAgreementRepository->loadInviteeAgreements($query->getInviteeUserGuid());
    }

}