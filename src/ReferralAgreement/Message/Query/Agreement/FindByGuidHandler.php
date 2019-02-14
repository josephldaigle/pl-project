<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 12:38 AM
 */


namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;


/**
 * Class FindByGuidHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class FindByGuidHandler
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * FindByGuidHandler constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param InviteeRepository           $inviteeRepository
     */
    public function __construct(ReferralAgreementRepository $referralAgreementRepository, InviteeRepository $inviteeRepository)
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * @param FindByGuid $query
     *
     * @return mixed
     *
     * @throws AgreementNotFoundException
     */
    public function __invoke(FindByGuid $query)
    {
        // replace with repo
        $referralAgreement = $this->referralAgreementRepository->findByGuid($query->getAgreementGuid());

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($query->getAgreementGuid());
        $referralAgreement->setInvitees($invitees);
        $referralCount= $this->referralAgreementRepository->getCurrentPeriodReferralCount($query->getAgreementGuid());
        $referralAgreement->setReferralCount($referralCount);

        return $referralAgreement;
    }
}