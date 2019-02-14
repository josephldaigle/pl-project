<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/10/18
 * Time: 12:06 PM
 */

namespace Test\Functional\Data\Command\Admin;


use PapaLocal\Data\Command\Admin\FetchTableNames;
use PapaLocal\Data\DataService;
use PapaLocal\Test\WebDatabaseTestCase;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Symfony\Bundle\FrameworkBundle\Client;


/**
 * FetchTableNamesTest.
 *
 * @package Test\Functional\Data\Command\Admin
 */
class FetchTableNamesTest extends WebDatabaseTestCase
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

	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testExportTablesAsCsvReturnsExpectedResultOnSuccess()
    {
        // exercise SUT
        $exportCmd = new FetchTableNames();
        $result = $this->persistence->execute($exportCmd);

        // make assertions
        $this->assertTrue(is_array($result), 'unexpected type');
        $this->assertGreaterThan(0, count($result), 'unexpected result count');
    }
}