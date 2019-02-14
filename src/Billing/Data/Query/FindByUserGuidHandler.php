<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 9:00 PM
 */


namespace PapaLocal\Billing\Data\Query;


use PapaLocal\Billing\Data\BillingProfileRepository;


/**
 * Class FindByUserGuidHandler
 *
 * @package PapaLocal\Billing\Data\Query
 */
class FindByUserGuidHandler
{
    /**
     * @var BillingProfileRepository
     */
    private $billingProfileRepository;

    /**
     * FindByUserGuidHandler constructor.
     *
     * @param BillingProfileRepository $billingProfileRepository
     */
    public function __construct(BillingProfileRepository $billingProfileRepository)
    {
        $this->billingProfileRepository = $billingProfileRepository;
    }

    /**
     * @param FindByUserGuid $query
     *
     * @return \PapaLocal\Entity\Entity
     */
    public function __invoke(FindByUserGuid $query)
    {
        return $this->billingProfileRepository->loadBillingProfileByUserGuid($query->getGuid(), true);
    }
}