<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 9:44 AM
 */

namespace PapaLocal\Core\Data;


use Doctrine\DBAL\Query\QueryBuilder;


/**
 * Class WrappedQueryBuilder
 *
 * Concrete QueryBuilder used throughout the application. Provides an integration point
 * for a third-party query builder tool to be used.
 *
 * @package PapaLocal\Core\Data
 */
class WrappedQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * WrappedQueryBuilder constructor.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function expr()
    {
        return $this->queryBuilder->expr();
    }

    public function getType()
    {
        return $this->queryBuilder->getType();
    }

    public function getState()
    {
        return $this->queryBuilder->getState();
    }

    public function execute()
    {
        return $this->queryBuilder->execute();
    }

    public function getSQL()
    {
        return $this->queryBuilder->getSQL();
    }

    public function setParameter($key, $value, $type = null): QueryBuilderInterface
    {
        $this->queryBuilder->setParameter($key, $value, $type);

        return $this;
    }

    public function setParameters(array $params, array $types = []): QueryBuilderInterface
    {
        $this->queryBuilder->setParameters($params, $types);

        return $this;
    }

    public function getParameters()
    {
        return $this->queryBuilder->getParameters();
    }

    public function getParameter($key)
    {
        return $this->queryBuilder->getParameter($key);
    }

    public function getParameterTypes()
    {
        return $this->queryBuilder->getParameterTypes();
    }

    public function getParameterType($key)
    {
        return $this->queryBuilder->getParameterType($key);
    }

    public function setFirstResult($firstResult): QueryBuilderInterface
    {
        $this->queryBuilder->setFirstResult($firstResult);

        return $this;
    }

    public function getFirstResult()
    {
        return $this->queryBuilder->getFirstResult();
    }

    public function setMaxResults($maxResults): QueryBuilderInterface
    {
        $this->queryBuilder->setMaxResults($maxResults);

        return $this;
    }

    public function getMaxResults()
    {
        return $this->queryBuilder->getMaxResults();
    }

    public function add($sqlPartName, $sqlPart, $append = false): QueryBuilderInterface
    {
        $this->queryBuilder->add($sqlPartName, $sqlPart, $append);

        return $this;
    }

    public function select($select = null): QueryBuilderInterface
    {
        $this->queryBuilder->select($select);

        return $this;
    }

    public function addSelect($select = null): QueryBuilderInterface
    {
        $this->queryBuilder->addSelect($select);

        return $this;
    }

    public function delete($delete = null, $alias = null): QueryBuilderInterface
    {
        $this->queryBuilder->delete($delete, $alias);

        return $this;
    }

    public function update($update = null, $alias = null): QueryBuilderInterface
    {
        $this->queryBuilder->update($update, $alias);

        return $this;
    }

    public function insert($insert = null): QueryBuilderInterface
    {
        $this->queryBuilder->insert($insert);

        return $this;
    }

    public function from($from, $alias = null): QueryBuilderInterface
    {
        $this->queryBuilder->from($from, $alias);

        return $this;
    }

    public function join($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface
    {
        $this->queryBuilder->join($fromAlias, $join, $alias, $condition);

        return $this;
    }

    public function innerJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface
    {
        $this->queryBuilder->innerJoin($fromAlias, $join, $alias, $condition);

        return $this;
    }

    public function leftJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface
    {
        $this->queryBuilder->leftJoin($fromAlias, $join, $alias, $condition);

        return $this;
    }

    public function rightJoin($fromAlias, $join, $alias, $condition = null): QueryBuilderInterface
    {
        $this->queryBuilder->rightJoin($fromAlias, $join, $alias, $condition);

        return $this;
    }

    public function set($key, $value): QueryBuilderInterface
    {
        $this->queryBuilder->set($key, $value);

        return $this;
    }

    public function where($predicates): QueryBuilderInterface
    {
        $this->queryBuilder->where($predicates);

        return $this;
    }

    public function andWhere($where): QueryBuilderInterface
    {
        $this->queryBuilder->andWhere($where);

        return $this;
    }

    public function orWhere($where): QueryBuilderInterface
    {
        $this->queryBuilder->orWhere($where);

        return $this;
    }

    public function groupBy($groupBy): QueryBuilderInterface
    {
        $this->queryBuilder->groupBy($groupBy);

        return $this;
    }

    public function addGroupBy($groupBy): QueryBuilderInterface
    {
        $this->queryBuilder->addGroupBy($groupBy);

        return $this;
    }

    public function setValue($column, $value): QueryBuilderInterface
    {
        $this->queryBuilder->setValue($column, $value);

        return $this;
    }

    public function values(array $values): QueryBuilderInterface
    {
        $this->queryBuilder->values($values);

        return $this;
    }

    public function having($having): QueryBuilderInterface
    {
        $this->queryBuilder->having($having);

        return $this;
    }

    public function andHaving($having): QueryBuilderInterface
    {
        $this->queryBuilder->andHaving($having);

        return $this;
    }

    public function orHaving($having): QueryBuilderInterface
    {
        $this->queryBuilder->orHaving($having);

        return $this;
    }

    public function orderBy($sort, $order = null): QueryBuilderInterface
    {
         $this->queryBuilder->orderBy($sort, $order);

         return $this;
    }

    public function addOrderBy($sort, $order = null): QueryBuilderInterface
    {
        $this->queryBuilder->addOrderBy($sort, $order);

        return $this;
    }

    public function getQueryPart($queryPartName)
    {
        return $this->queryBuilder->getQueryPart($queryPartName);
    }

    public function getQueryParts()
    {
        return $this->queryBuilder->getQueryParts();
    }

    public function resetQueryParts($queryPartNames = null)
    {
        return $this->queryBuilder->resetQueryParts($queryPartNames);
    }

    public function resetQueryPart($queryPartName): QueryBuilderInterface
    {
        $this->queryBuilder->resetQueryPart($queryPartName);

        return $this;
    }

    public function createNamedParameter($value, $type = \PDO::PARAM_STR, $placeHolder = null)
    {
        return $this->queryBuilder->createNamedParameter($value, $type, $placeHolder);
    }

    public function createPositionalParameter($value, $type = \PDO::PARAM_STR)
    {
        return $this->queryBuilder->createPositionalParameter($value, $type);
    }

    public function __toString()
    {
        return $this->queryBuilder->__toString();
    }

    /**
     * The current concrete query builder class is provided by Doctrine\Dbal.
     *
     * The two functions below facilitate proper cloning of this class, and
     * are necessary to borrow the cloning functionality of the Dbal\QueryBuilder,
     * which contains code that must be executed when cloning.
     */

    /**
     * @return WrappedQueryBuilder
     */
    public function __clone()
    {
        return $this->cloneWrappedBuilder();
    }

    /**
     * @return WrappedQueryBuilder
     */
    private function cloneWrappedBuilder(): WrappedQueryBuilder
    {
        $qb = clone $this->queryBuilder;
        return new self($qb);
    }
}