<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/6/18
 * Time: 5:18 PM
 */

namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\UpdatePersonPhone;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * UpdatePersonPhoneTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class UpdatePersonPhoneTest extends WebDatabaseTestCase
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

    public function testUpdatePersonPhoneReturnsNumRowsOnSuccess()
    {
        // set up fixtures
        $begPhoneCount = $this->getConnection()->getRowCount('PhoneNumber');
        $begPersPhCount = $this->getConnection()->getRowCount('R_PersonPhoneNumber');

        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('5555555555')
            ->setType(AttrType::PHONE_MAIN);

        $personId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT id FROM Person WHERE id IN (SELECT personId from R_PersonPhoneNumber) LIMIT 1')
            ->getRow(0)['id']);

        //exercise SUT
        $createPhoneCmd = new UpdatePersonPhone($personId, $phoneNumber);
        $result = $this->persistence->execute($createPhoneCmd);

        // make assertions
        $this->assertTableRowCount('PhoneNumber', $begPhoneCount + 1,
            'Phone table not incremented');
        $this->assertTableRowCount('R_PersonPhoneNumber', $begPersPhCount,
            'unexpected row count in R_PersonPhone');

        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
    }
}