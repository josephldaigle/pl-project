<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/11/18
 * Time: 12:41 PM
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\CreatePersonPhone;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreatePersonPhoneTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class CreatePersonPhoneTest extends WebDatabaseTestCase
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
		    'Person',
		    'PhoneNumber',
		    'L_PhoneNumberType',
		    'R_PersonPhoneNumber'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreatePersonPhoneReturnsPhoneNumberWithIdOnSuccess()
    {
        // set up fixtures
        $begPhoneCount = $this->getConnection()->getRowCount('PhoneNumber');
        $begPersPhCount = $this->getConnection()->getRowCount('R_PersonPhoneNumber');

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('5555555555')
            ->setType(AttrType::PHONE_MAIN);

        $personId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT id FROM Person WHERE id NOT IN (SELECT personId FROM R_PersonPhoneNumber) LIMIT 1')
            ->getRow(0)['id']);

        //exercise SUT
        $createPhoneCmd = new CreatePersonPhone($personId, $phoneNumber);
        $result = $this->persistence->execute($createPhoneCmd);

        // make assertions
        $this->assertTableRowCount('PhoneNumber', $begPhoneCount + 1, 'Phone table not incremented');
        $this->assertTableRowCount('R_PersonPhoneNumber', $begPersPhCount + 1, 'R_PersonPhone not incremented');

        $this->assertInstanceOf(PhoneNumber::class, $result, 'unexpected instance type');
        $this->assertObjectHasAttribute('id', $result, 'result does not have id property');
        $this->assertFalse(is_null($result->getId()), 'result is missing an id');
    }

	/**
	 * @expectedException Doctrine\DBAL\Exception\UniqueConstraintViolationException
	 * @expectedExceptionMessageRegExp /(Duplicate entry)/
	 */
    public function testCreatePersonPhoneDoesNotCreateNewPhoneNumberWhenExistingNumberSupplied()
    {
        // set up fixtures
        $begPhoneCount = $this->getConnection()->getRowCount('PhoneNumber');
        $begPersPhCount = $this->getConnection()->getRowCount('R_PersonPhoneNumber');

        $phoneArr = $this->getConnection()
            ->createQueryTable('phone', 'SELECT * FROM PhoneNumber LIMIT 1')
            ->getRow(0);

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber($phoneArr['phoneNumber'])
            ->setType(AttrType::PHONE_MAIN);

        $personId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0)['id']);

        //exercise SUT
        $createPhoneCmd = new CreatePersonPhone($personId, $phoneNumber);
        $this->persistence->execute($createPhoneCmd);
    }
}