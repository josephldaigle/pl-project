<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 10:21 PM
 */

namespace PapaLocal\Core\Data;


/**
 * Interface TableGatewayInterface
 *
 * Describe an interface for accessing Table data.
 *
 * @package PapaLocal\Core\Data
 */
interface TableGatewayInterface extends TransactionalInterface
{
    public function setTable(string $tableName);

    public function create(array $record);

    public function update(array $record);

    public function delete(int $id);

    public function findBy(string $column, $value): RecordSetInterface;

    public function findById(int $id): RecordInterface;

    public function findByGuid(string $guid): RecordInterface;

    public function findAllOrderedById(): RecordSetInterface;

    public function findByColumns(array $filter): RecordSetInterface;

    public function getQueryBuilder(): QueryBuilderInterface;
}