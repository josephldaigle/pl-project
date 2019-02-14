<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/16/18
 * Time: 6:49 AM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Company;

/**
 * LoadCompany.
 */
class LoadCompany extends QueryCommand
{
    /**
     * @var int $companyId
     */
    private $companyId;

    /**
     * LoadCompany constructor.
     *
     * @param int $companyId
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            // find the company
            $this->tableGateway->setTable('Company');
            $row = $this->tableGateway->findById($this->companyId);

            if (count($row) < 1) {
                return null;
            }

            return $this->serializer->denormalize($row[0], Company::class, 'array');

        } catch (\Exception $exception) {
            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}