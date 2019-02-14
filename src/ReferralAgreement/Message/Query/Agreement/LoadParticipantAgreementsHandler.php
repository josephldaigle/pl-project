<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/29/18
 * Time: 1:41 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;

use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;


/**
 * Class LoadParticipantAgreementsHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadParticipantAgreementsHandler
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * LoadParticipantAgreementsHandler constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     */
    public function __construct(ReferralAgreementRepository $referralAgreementRepository)
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
    }

    /**
     * @param LoadParticipantAgreements $query
     *
     * @return mixed
     */
    public function __invoke(LoadParticipantAgreements $query)
    {
        return $this->referralAgreementRepository->loadParticipantAgreements($query->getParticipantUserGuid());
    }


}