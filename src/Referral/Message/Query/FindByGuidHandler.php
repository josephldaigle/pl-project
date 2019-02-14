<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 5:18 PM
 */

namespace PapaLocal\Referral\Message\Query;


use PapaLocal\Referral\ReferralService;


/**
 * Class FindByGuidHandler
 * @package PapaLocal\Referral\Message\Query
 */
class FindByGuidHandler
{
    /**
     * @var ReferralService
     */
    private $referralService;

    /**
     * FindByGuid constructor.
     * @param ReferralService $referralService
     */
    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * @inheritDoc
     */
    function __invoke(FindByGuid $query)
    {
        return $this->referralService->findByGuid($query->getGuid());
    }
}