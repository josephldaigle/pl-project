<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 7:32 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\Company;


use PapaLocal\IdentityAccess\Data\Repository\CompanyRepository;


/**
 * Class FindByUserGuidHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Query\Company
 */
class FindByUserGuidHandler
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * FindByUserGuidHandler constructor.
     *
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param FindByUserGuid $query
     *
     * @return mixed
     */
    function __invoke(FindByUserGuid $query)
    {
        return $this->companyRepository->findByUserGuid($query->getUserId());
    }
}