<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/11/18
 * Time: 3:56 PM
 */


namespace Test\Functional\Data\Command\Admin;


use PapaLocal\Data\Command\Admin\FetchTableData;
use PapaLocal\Data\DataService;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * FetchTableDataTest.
 *
 * @package Test\Functional\Data\Command\Admin
 */
class FetchTableDataTest extends WebDatabaseTestCase
{
    /**
     * @var DataService
     */
    private $persistence;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Person'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');

    }

    public function testFetchTableDataReturnsAllRowsFromTable()
    {
        // set up fixtures
        $tableRowCount = $this->getConnection()->getRowCount('Person');
        $colNames = $this->getConnection()->getMetaData()->getTableColumns('Person');

        // exercise SUT
        $cmd = new FetchTableData('Person');
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertTrue(is_array($result), 'unexpected type');
        $this->assertCount($tableRowCount, $result, 'unexpected count');
        $this->assertSame($colNames, array_keys($result[0]), 'column names not correct');
    }
}