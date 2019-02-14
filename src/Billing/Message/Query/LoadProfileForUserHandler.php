<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 5:15 PM
 */


namespace PapaLocal\Billing\Message\Query;


use PapaLocal\Billing\Data\BillingProfileRepository;


/**
 * Class LoadProfileForUserHandler
 *
 * @package PapaLocal\Billing\Message\Query
 */
class LoadProfileForUserHandler
{
    /**
     * @var BillingProfileRepository
     */
    private $billingProfileRepository;

    /**
     * LoadProfileForUserHandler constructor.
     *
     * @param BillingProfileRepository $billingProfileRepository
     */
    public function __construct(BillingProfileRepository $billingProfileRepository)
    {
        $this->billingProfileRepository = $billingProfileRepository;
    }

    /**
     * @param LoadProfileForUser $query
     *
     * @return mixed
     */
    public function __invoke(LoadProfileForUser $query)
    {
        return $this->billingProfileRepository->loadBillingProfileByUserGuid($query->getUserGuid(), true);
    }
}