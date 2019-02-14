<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/5/18
 * Time: 7:18 PM
 */

namespace PapaLocal\Data\Command\User;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\ClassFactory;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;

/**
 * LoadUserCompanies.
 *
 * Loads a Collection of the companies owned by $userId.
 */
class LoadUserCompanies extends QueryCommand
{
    /**
     * @var int
     */
    private $userId;

    /**
     * LoadUserCompanies constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // instantiate list
        $companyList = ClassFactory::create(Collection::class);

        // fetch owned companies
        $this->tableGateway->setTable('v_company_owner');
        $viewRows =  $this->tableGateway->findBy('userId', $this->userId);

        // return empty collection if none owned
        if (count($viewRows) < 1) {
            return $companyList;
        }

        // load companies into list
        $this->tableGateway->setTable('Company');
        foreach ($viewRows as $row) {
            $companyRow = $this->tableGateway->findById($row['id']);

            if (count($companyRow) > 0) {
                $company = $this->serializer->denormalize($companyRow[0], Company::class, 'array');
                $companyList->add($company);
            }
        }


        return $companyList;
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}