<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/30/18
 */

namespace Test\Integration\Data;


use PapaLocal\Core\Data\AdaptedTableGateway;
use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class DatabaseInputOutputTest.
 *
 * This test case ensures that the application can read and write from the database.
 *
 * @package Test\Integration\Data
 */
class DatabaseInputOutputTest extends WebDatabaseTestCase
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configureDataSet(array(
            'Person',
            'User'
        ));

        parent::setUp();

        $this->tableGateway = $this->diContainer->get('papalocal_data.adapted_table_gateway');
    }

    public function testCanCreateRow()
    {
        $begRowCount = $this->getConnection()->getRowCount('Person');

        $data = array(
            'id' => null,
            'guid' => '2782442e-f03d-4699-a94b-3e9ec8a4a172',
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'about' => '',
            'timeCreated' => ''
        );

        $this->tableGateway->setTable('Person');
        $this->tableGateway->create($data);

        $this->assertTableRowCount('Person', $begRowCount + 1);
    }

    public function testCanFindByGuid()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('userGuid', 'SELECT guid FROM User LIMIT 1')
            ->getRow(0)['guid'];

        // exercise SUT
        $this->tableGateway->setTable('User');
        $result = $this->tableGateway->findByGuid($guid);

        // make assertions
        $this->assertInstanceOf(Record::class, $result, 'unexpected type');
        $this->assertArrayHasKey('id', $result);
    }
}