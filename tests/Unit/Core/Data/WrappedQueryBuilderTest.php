<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 7:25 PM
 */

namespace Test\Unit\Core\Data;


use Doctrine\DBAL\Query\QueryBuilder;
use PapaLocal\Core\Data\WrappedQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class WrappedQueryBuilderTest
 *
 * @package Test\Unit\Core\Data
 */
class WrappedQueryBuilderTest extends TestCase
{
    public function delegationProvider()
    {
        return [
            'testGetState' => ['getState', 'getState'],
            'testExecute' => ['execute', 'execute'],
            'testGetSQL' => ['getSQL', 'getSQL'],
            'testSetParameter' => ['setParameter', 'setParameter', ['testKey', 'testVal', null]],
            'testSetParameters' => ['setParameters', 'setParameters', [[':user_id1' => 1, ':user_id2' => 2], []]],
            'testGetParameters' => ['getParameters', 'getParameters'],
            'testGetParameter' => ['getParameter', 'getParameter', ['testKey']],
            'testGetParameterTypes' => ['getParameterTypes', 'getParameterTypes'],
            'testGetParameterType' => ['getParameterType', 'getParameterType', ['testKey']],
            'testSetFirstResult' => ['setFirstResult', 'setFirstResult', ['firstResult']],
            'testGetFirstResult' => ['getFirstResult', 'getFirstResult'],
            'testSetMaxResults' => ['setMaxResults', 'setMaxResults', [5]],
            'testGetMaxResults' => ['getMaxResults', 'getMaxResults'],
            'testAdd' => ['add', 'add', ['sqlPartName', 'sqlPart']],
            'testSelect' => ['select', 'select'],
            'testAddSelect' => ['addSelect', 'addSelect'],
            'testDelete' => ['delete', 'delete'],
            'testUpdate' => ['update', 'update'],
            'testInsert' => ['insert', 'insert'],
            'testFrom' => ['from', 'from', ['tableName']],
            'testJoin' => ['join', 'join', ['fromAlias', 'table', 'alias', 'fromAlias.col = alias.col']],
            'testInnerJoin' => ['innerJoin', 'innerJoin', ['fromAlias', 'table', 'alias', 'fromAlias.col = alias.col']],
            'testLeftJoin' => ['leftJoin', 'leftJoin', ['fromAlias', 'table', 'alias', 'fromAlias.col = alias.col']],
            'testRightJoin' => ['rightJoin', 'rightJoin', ['fromAlias', 'table', 'alias', 'fromAlias.col = alias.col']],
            'testSet' => ['set', 'set', ['key', 'value']],
            'testWhere' => ['where', 'where', ['alias.col = 1']],
            'testAndWhere' => ['andWhere', 'andWhere', ['alias.col = 1']],
            'testOrWhere' => ['orWhere', 'orWhere', ['alias.col = 1']],
            'testGroupBy' => ['groupBy', 'groupBy', ['alias.col']],
            'testAddGroupBy' => ['addGroupBy', 'addGroupBy', ['alias.col']],
            'testSetValue' => ['setValue', 'setValue', ['col', 'val']],
            'testValues' => ['values', 'values', [['val1', 'val2']]],
            'testHaving' => ['having', 'having', ['alias.col = 1']],
            'testAndHaving' => ['andHaving', 'andHaving', ['alias.col = 1']],
            'testOrHaving' => ['orHaving', 'orHaving', ['alias.col = 1']],
            'testOrderBy' => ['orderBy', 'orderBy', ['alias.col', 'DESC']],
            'testAddOrderBy' => ['addOrderBy', 'addOrderBy', ['alias.col', 'DESC']],
            'testGetQueryPart' => ['getQueryPart', 'getQueryPart', ['partName']],
            'testGetQueryParts' => ['getQueryParts', 'getQueryParts'],
            'testResetQueryParts' => ['resetQueryParts', 'resetQueryParts'],
            'testResetQueryPart' => ['resetQueryPart', 'resetQueryPart', ['partName']],
            'testCreateNamedParameter' => ['createNamedParameter', 'createNamedParameter', ['testVal']],
            'testCreatePositionalParameter' => ['createPositionalParameter', 'createPositionalParameter', ['testVal']],
            'testToString' => ['__toString', '__toString']
        ];
    }

    /**
     * @dataProvider delegationProvider
     *
     * @param string $wrappedMethodName
     * @param string $delegateMethodName
     * @param array  $args
     */
    public function testWrapperDelegatesToWrapped(string $wrappedMethodName, string $delegateMethodName, array $args = [])
    {
        $queryBuilderMock = $this->createMock(QueryBuilder::class);

        $wrappedQb = new WrappedQueryBuilder($queryBuilderMock);

        if (empty($args)) {
            $queryBuilderMock->expects($this->once())
                             ->method($delegateMethodName);

            $wrappedQb->$wrappedMethodName();

        } else {
            $queryBuilderMock->expects($this->once())
                             ->method($delegateMethodName)
                            ->with(...$args);

            $wrappedQb->$wrappedMethodName(...$args);
        }
    }
}