<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 10:15 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;


/**
 * Class LoadUserAgreementsHandler
 *
 * @package ReferralAgreement\Message\Query\Agreement
 */
class LoadUserAgreementsHandler
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * LoadUserAgreementsHandler constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     */
    public function __construct(ReferralAgreementRepository $referralAgreementRepository)
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
    }

    /**
     * @param LoadUserAgreements $query
     *
     * @return Collection
     */
    function __invoke(LoadUserAgreements $query): Collection
    {
        return $this->referralAgreementRepository->loadUserAgreements($query->getOwnerGuid());
    }
}