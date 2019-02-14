<?php
/**
 * Created by Joseph Daigle.
 * Date: 2/3/19
 * Time: 6:40 PM
 */

namespace PapaLocal\Referral\Message\Query;


use PapaLocal\Referral\Data\ReferralRepository;


/**
 * FindByAgreementGuidHandler.
 *
 * @package PapaLocal\Referral\Message\Query
 */
class FindByAgreementGuidHandler
{
    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * FindByAgreementGuidHandler constructor.
     *
     * @param ReferralRepository $referralRepository
     */
    public function __construct(ReferralRepository $referralRepository)
    {
        $this->referralRepository = $referralRepository;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(FindByAgreementGuid $query)
    {
        return $this->referralRepository->fetchByAgreementGuid($query->getAgreementGuid());
    }


}