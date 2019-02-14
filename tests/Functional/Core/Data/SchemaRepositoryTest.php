<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/17/18
 * Time: 8:24 PM
 */


namespace Test\Functional\Core\Data;


use PapaLocal\Core\Data\SchemaRepository;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class SchemaRepositoryTest
 *
 * @package Test\Functional\Core\Data
 */
class SchemaRepositoryTest extends WebDatabaseTestCase
{
	/**
	 * @var SchemaRepository
	 */
	private $schemaRepository;

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$this->configureDataSet([]);

		parent::setUp();

		$this->client->followRedirects();

		$this->schemaRepository = $this->diContainer->get('PapaLocal\Core\Data\SchemaRepository');
	}

	public function testFetchColumnNamesReturnsCorrectResultOnSuccess()
	{
		// set up fixtures
		$columns = $this->getConnection()->getMetaData()->getTableColumns('Person');


		// exercise SUT
		$result = $this->schemaRepository->fetchColumnNames('Person');

		// make assertions
		$this->assertTrue(is_array($result), 'unexpected type');
		$this->assertEquals($columns, $result, 'unexpected value');
	}
}