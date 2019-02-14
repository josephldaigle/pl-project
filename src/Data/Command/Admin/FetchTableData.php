<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/10/18
 * Time: 1:58 PM
 */

namespace PapaLocal\Data\Command\Admin;

use PapaLocal\Data\Command\QueryCommand;

/**
 * FetchTableData.
 *
 * @package PapaLocal\Data\Command\Admin
 */
class FetchTableData extends QueryCommand
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * FetchTableData constructor.
     *
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {

            $this->tableGateway->setTable($this->tableName);
            return $this->tableGateway->findAllOrderedById();

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