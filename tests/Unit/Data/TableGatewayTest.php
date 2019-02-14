<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 10:21 PM
 */

namespace Test\Unit\Data;

use PapaLocal\Core\Data\TableGateway;
use Doctrine\DBAL\DBALException;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use PapaLocal\Test\AbstractDatabaseTestCase;

/**
 * TableGatewayTest.
 */
class TableGatewayTest extends AbstractDatabaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->personId = intval(($this->getConnection()
            ->createQueryTable('p', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0))['id']);

        $this->person = $this->getConnection()
            ->createQueryTable('person', 'SELECT * FROM Person LIMIT 1')
            ->getRow(0);
    }


    /**
     * Called by setUp function before each test case.
     *
     * @return CsvDataSet
     */
    protected function getDataSet()
    {
        $dir = $GLOBALS['DB_CSV_DIR'];

        // create a new CSV data set
        $dataSet = new CsvDataSet();

        $dataSet->addTable('Address', $dir . "Address.csv");
        $dataSet->addTable('Person', $dir . "Person.csv");
        $dataSet->addTable('EmailAddress', $dir . "EmailAddress.csv");
        $dataSet->addTable('L_EmailAddressType', $dir . "L_EmailAddressType.csv");
        $dataSet->addTable('R_PersonEmailAddress', $dir . "R_PersonEmailAddress.csv");

        return $dataSet;
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Column not found)(.)+(Unknown column)/
     */
    public function testCreateThrowsExceptionWhenColumnSuppliedDoesNotExist()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['create', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');
        
        

        $this->person['badColumn'] = $this->person['firstName'];
        unset($this->person['id']);
        unset($this->person['firstName']);

        //exercise SUT
        $tblGateMock->create($this->person);
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Integrity constraint violation)/
     */
    public function testCreateThrowsExceptionWhenRequiredValueNotSupplied()
    {
        //set up fixtures
        //mock and configure SUT
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['create', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //prep test data
        unset($this->person['id']);
        $this->person['firstName'] = null;

        //exercise SUT
        $tblGateMock->create($this->person);
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Integrity constraint violation)(.)+(Duplicate entry)/
     */
    public function testCreateThrowsExceptionWhenRecordExists()
    {
        //set up fixtures
        //mock and configure SUT
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['create', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $tblGateMock->create($this->person);
    }

    public function testCreateReturnsIdOnSuccess()
    {
        //set up fixtures
        //get table stats prior to write operation
        $begRowCount = $this->getConnection()->getRowCount('Person');
        $maxId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0)['id']);

        //set up SUT
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['create', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //prep test data
        unset($this->person['id']);
        $this->person['guid'] = '83c61760-e169-46c7-920b-914dbd0fc31a';

        //exercise SUT
        $id = $tblGateMock->create($this->person);

        //assert num rows affected is 1
        $this->assertTrue(is_int($id));
        $this->assertEquals($maxId + 1, $id, 'id mismatch');

        //assert table row count
        $this->assertTableRowCount('Person', $begRowCount + 1, 'row count mismatch');
    }

    /**
     * When saving to the database, there is no enforcement on empty string values being
     * inserted into NOT NULL VARCHAR columns. As shown in this test, the query will
     * still produce a new row.
     */
    public function testCreateReturnsNewRecordIdWhenNoValuesSupplied()
    {
        //set up fixtures
        //get table stats prior to write operation
        $begRowCount = $this->getConnection()->getRowCount('Person');
        $maxId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0)['id']);

        //set up SUT
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['create', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //prep test data
        $person = array(
            'firstName' => '',
            'lastName' => '',
            'about' => '',
        );

        //exercise SUT
        $id = $tblGateMock->create($person);

        $lastRow = $this->getConnection()
            ->createQueryTable('last_row', 'SELECT * FROM Person WHERE id = (SELECT MAX(id) FROM Person)')
            ->getRow(0);

        //assert num rows affected is 1
        $this->assertTrue(is_int($id));
        $this->assertEquals($maxId + 1, $id, 'id mismatch');

        //assert table row count
        $this->assertTableRowCount('Person', $begRowCount + 1, 'row count mismatch');
    }


    //TODO: are these really functional tests?
//    /**
//     * @expectedException \PHPUnit\Framework\Error\Error
//     * @expectedExceptionMessageRegExp /^(Undefined index: id)$/
//     */
//    public function testUpdateThrowsExceptionWhenParamDoesNotContainAnId()
//    {
//        //set up fixtures
//        $tblGateMock = $this->getMockBuilder(TableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update', 'setTable'])
//            ->getMock();
//        $tblGateMock->setTable('Person');
//
//        //prep test data
//        unset($this->person['id']);
//        $this->person['firstName'] = 'newFirstName';
//
//        //exercise SUT
//        $tblGateMock->update($this->person);
//    }


    /**
     * @expectedException \PHPUnit\Framework\Error\Error
     * @expectedExceptionMessageRegExp /^(Undefined index: id)$/
     */
//    public function testUpdateThrowsExceptionWhenParamIsEmpty()
//    {
//        //set up fixtures
//        $tblGateMock = $this->getMockBuilder(TableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update', 'setTable'])
//            ->getMock();
//        $tblGateMock->setTable('Person');
//
//        //exercise SUT
//        $tblGateMock->update(array());
//    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Column not found)(.)+(Unknown column)/
     */
    public function testUpdateThrowsExceptionWhenColumnSuppliedDoesNotExist()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['update', 'setTable'])
            ->getMock();

        $tblGateMock->setTable('Person');

        //prep test data
        unset($this->person['firstName']);
        $this->person['badColumn'] = 'someValue';

        //exercise SUT
        $tblGateMock->update($this->person);
    }

//    public function testUpdateReturnsZeroWhenParamHasKeysOnly()
//    {
//        //set up fixtures
//        $tblGateMock = $this->getMockBuilder(TableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update', 'setTable'])
//            ->getMock();
//        $tblGateMock->setTable('Person');
//
//        //prep test data
//        foreach ($this->person as $key => $val) {
//            $this->person[$key] = null;
//        }
//
//        //exercise SUT
//        $res = $tblGateMock->update($this->person);
//
//        //make assertions
//        $this->assertTrue(is_int($res));
//        $this->assertEquals(0, $res);
//    }

//    public function testUpdateIsSuccessfulWhenParamIsEmpty()
//    {
//        //set up fixtures
//        $tblGateMock = $this->getMockBuilder(TableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update', 'setTable'])
//            ->getMock();
//        $tblGateMock->setTable('Person');
//
//        //prep test data
//        foreach ($this->person as $key => $val) {
//            $this->person[$key] = null;
//        }
//
//        //exercise SUT
//        $res = $tblGateMock->update($this->person);
//
//        //make assertions
//        $this->assertTrue(is_int($res));
//        $this->assertEquals(0, $res);
//    }

    //TODO: this is only applicable to R_Tables
    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Integrity constraint violation)(.)+(foreign key constraint fails)(.)+/
     */
//    public function testUpdateThrowsExceptionWhenColumnSuppliedNotValidForeignKey()
//    {
//        //set up fixtures
//
//        //create a personEmailAddress record
//        $personEmail = array(
//            'id' => $this->personEmailId,
//            'personId' => $this->personId + 1,
//            'emailId' => 1,
//            'typeId' => 1
//        );
//
//        //create a TableGateway mock
//        $tblGateMock = $this->getMockBuilder(PersonEmailAddressTableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update'])
//            ->getMock();
//
//        //exercise SUT
//        $res = $tblGateMock->update($personEmail);
//    }
//
//    /**
//     * @expectedException \Doctrine\DBAL\DBALException
//     * @expectedExceptionMessageRegExp /(Integrity constraint violation)(.)+(foreign key constraint fails)(.)+/
//     */
//    public function testUpdateThrowsExceptionWhenMissingRequiredValue()
//    {
//        //set up fixtures
//
//        //create a personEmailAddress record
//        $personEmail = array(
//            'id' => $this->personEmailId,
//            'personId' => null,
//            'emailId' => 1,
//            'typeId' => 1
//        );
//
//        //create a TableGateway mock
//        $tblGateMock = $this->getMockBuilder(PersonEmailAddressTableGateway::class)
//            ->setConstructorArgs(array($this->getDbal()))
//            ->setMethodsExcept(['update'])
//            ->getMock();
//
//        //exercise SUT
//        $res = $tblGateMock->update($personEmail);
//    }
//

    /**
     *
     */
    public function testUpdateReturnsCorrectNumberRowsOnSuccess()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['update', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //prep test data
        $this->person['firstName'] = 'newName';

        //exercise SUT
        $res = $tblGateMock->update($this->person);

        //make assertions
        $this->assertTrue(is_int($res));
        $this->assertEquals(1, $res, 'unexpected num rows');
    }


    public function testDeleteReturnsZeroWhenIdDoesNotExists()
    {
        //get beginning row count
        $begRowCount = $this->getConnection()->getRowCount('Person');

        $maxId = intval(($this->getConnection()
            ->createQueryTable('u', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0))['id']);

        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['delete', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        $this->person['id'] = $maxId;

        //exercise SUT
        $numRows = $tblGateMock->delete($maxId + 1);

        //assert num rows affected
        $this->assertEquals(0, $numRows, 'unexpected num rows');

        //assert ending row count
        $this->assertTableRowCount('Person', $begRowCount);
    }

    /**
     * Test the delete function throws exception when the record being deleted is
     * a participant in a foreign key relationship.
     *
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Cannot delete or update)(.)+(a foreign key constraint fails)/
     */
    public function testDeleteThrowsExceptionWhenRecordIsForeignKeyReference()
    {
        //setup fixtures
        $begRowCount = $this->getConnection()->getRowCount('Person');

        $personId = intval(($this->getConnection()
            ->createQueryTable('p', 'SELECT MAX(personId) as \'id\' FROM R_PersonEmailAddress')
            ->getRow(0))['id']);

        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['delete', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $numRows = $tblGateMock->delete($personId);

        //assert num rows affected
        $this->assertEquals(0, $numRows, 'unexpected num rows');

        //assert ending row count
        $this->assertTableRowCount('Person', $begRowCount, 'row count mismatched');
    }

    public function testDeleteIsSuccess()
    {
        //setup fixtures
        $begRowCount = $this->getConnection()->getRowCount('Address');

        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['delete', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Address');

        $addressId = intval($this->getConnection()
            ->createQueryTable('pid', 'SELECT MAX(id) as \'id\' FROM Address')
            ->getRow(0)['id']);

        //exercise SUT
        $numRows = $tblGateMock->delete($addressId);

        //assert num rows affected
        $this->assertEquals(1, $numRows, 'unexpected num rows');

        //assert ending row count
        $this->assertTableRowCount('Address', $begRowCount - 1, 'row count mismatched');
    }

    public function testFindByIdReturnsEmptyArrayWhenIdDoesNotExist()
    {
        //set up text fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findById', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        $maxId = intval(($this->getConnection()
            ->createQueryTable('u', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0))['id']);

        //exercise SUT
        $row = $tblGateMock->findById($maxId + 1);

        //assert return value
        $this->assertTrue(is_array($row), 'result not array');
        $this->assertEmpty($row, 'result set not empty');
    }

    public function testFindByIdIsSuccess()
    {
        //set up text fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findById', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $row = $tblGateMock->findById($this->person['id']);

        //assert return value
        $this->assertTrue(is_array($row), 'result not array');
        $this->assertNotEmpty($row, 'result set is empty');

        foreach(array_keys($this->person) as $key) {
            $this->assertArrayHasKey($key, $row[0]);
            $this->assertEquals($this->person[$key], $row[0][$key]);
        }
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Column not found)(.)+(Unknown column)/
     */
    public function testFindByThrowExceptionWhenNonExistingColumnSpecified()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findBy', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $tblGateMock->findBy('badColumnName', 1);
    }

    public function testFindByReturnsEmptyArrayWhenNoRecordsFound()
    {
        //set up fixtures
        $maxId = intval(($this->getConnection()
            ->createQueryTable('email', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0))['id']);

        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findBy', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $row = $tblGateMock->findBy('id', $maxId + 1);

        //assert return value
        $this->assertTrue(is_array($row), 'result not an array');
        $this->assertEmpty($row, 'result set not empty');
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Syntax error or access violation)(.)+(You have an error in your SQL syntax)/
     */
    public function testFindByThrowsExceptionWhenColumnIsEmptyString()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findBy', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $tblGateMock->findBy('', 1);
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Column not found)(.)+(Unknown column)/
     */
    public function testFindByColumnsWhenColumnSuppliedDoesNotExist()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findByColumns', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $tblGateMock->findByColumns(array('badColumn' => 1, 'firstName' => 'Joe'));
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Syntax error or access violation)(.)+(You have an error in your SQL syntax)/
     */
    public function testFindByColumnsWhenColumnSuppliedIsEmptyString()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findByColumns', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $tblGateMock->findByColumns(array('' => 1));
    }

    public function testFindAllOrderedByIdIsSuccess()
    {
        //set up fixtures
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->setConstructorArgs(array($this->getDbal()))
            ->setMethodsExcept(['findAllOrderedById', 'setTable'])
            ->getMock();
        $tblGateMock->setTable('Person');

        //exercise SUT
        $rows = $tblGateMock->findAllOrderedById();

        //make assertions
        for ($i = 0; $i < count($rows); $i++) {
            $fixture = $this->getConnection()
                ->createQueryTable('p', 'SELECT * FROM Person ORDER BY id ASC')
                ->getRow($i);

            $this->assertEquals($fixture['id'], $rows[$i]['id']);
        }
    }
}