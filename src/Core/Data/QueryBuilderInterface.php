<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 9:23 AM
 */

namespace PapaLocal\Core\Data;


/**
 * Interface QueryBuilderInterface
 *
 * Describe a query builder.
 *
 * This interface mimic the public interface of Doctrine's Query\QueryBuilder class.
 *
 * @package PapaLocal\Core\Data
 */
interface QueryBuilderInterface
{
    public function expr();

    public function getType();

    public function getState();

    public function execute();

    public function getSQL();

    public function setParameter($key, $value, $type = null): QueryBuilderInterface;

    public function setParameters(array $params, array $types = []): QueryBuilderInterface;

    public function getParameters();

    public function getParameter($key);

    public function getParameterTypes();

    public function getParameterType($key);

    public function setFirstResult($firstResult): QueryBuilderInterface;

    public function getFirstResult();

    public function setMaxResults($maxResults): QueryBuilderInterface;

    public function getMaxResults();

    public function add($sqlPartName, $sqlPart, $append = false): QueryBuilderInterface;

    public function select($select = null): QueryBuilderInterface;

    public function addSelect($select = null): QueryBuilderInterface;

    public function delete($delete = null, $alias = null): QueryBuilderInterface;

    public function update($update = null, $alias = null): QueryBuilderInterface;

    public function insert($insert = null): QueryBuilderInterface;

    public function from($from, $alias = null): QueryBuilderInterface;

    public function join($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface;

    public function innerJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface;

    public function leftJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface;

    public function rightJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface;

    public function set($key, $value): QueryBuilderInterface;

    public function where($predicates): QueryBuilderInterface;

    public function andWhere($where): QueryBuilderInterface;

    public function orWhere($where): QueryBuilderInterface;

    public function groupBy($groupBy): QueryBuilderInterface;

    public function addGroupBy($groupBy): QueryBuilderInterface;

    public function setValue($column, $value): QueryBuilderInterface;

    public function values(array $values): QueryBuilderInterface;

    public function having($having): QueryBuilderInterface;

    public function andHaving($having): QueryBuilderInterface;

    public function orHaving($having): QueryBuilderInterface;

    public function orderBy($sort, $order = null): QueryBuilderInterface;

    public function addOrderBy($sort, $order = null): QueryBuilderInterface;

    public function getQueryPart($queryPartName);

    public function getQueryParts();

    public function resetQueryParts($queryPartNames = null);

    public function resetQueryPart($queryPartName): QueryBuilderInterface;

    public function createNamedParameter($value, $type = \PDO::PARAM_STR, $placeHolder = null);

    public function createPositionalParameter($value, $type = \PDO::PARAM_STR);

    public function __toString();

    public function __clone();
}