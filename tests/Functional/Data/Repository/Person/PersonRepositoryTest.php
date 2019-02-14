<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/16/18
 */


namespace Test\Functional\Data\Repository\Person;


use PapaLocal\Data\Repository\Person\PersonRepository;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class PersonRepositoryTest.
 *
 * @package Test\Functional\Data\Repository\Person
 */
class PersonRepositoryTest extends WebDatabaseTestCase
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'L_EmailAddressType',
		    'EmailAddress',
		    'Person',
		    'R_PersonEmailAddress'
	    ]);
        parent::setUp();

        $this->personRepository = $this->diContainer->get('PapaLocal\Data\Repository\Person\PersonRepository');
    }

    public function testPersonExistsReturnsTrueWhenFound()
    {
        // set up fixtures
        $persArr = $this->getConnection()
            ->createQueryTable('person', 'SELECT * FROM v_person_email_address LIMIT 1')
            ->getRow(0);

        // exercise SUT
        $result = $this->personRepository->personExists($persArr['firstName'], $persArr['lastName'], $persArr['emailAddress']);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertTrue($result, 'unexpected value');
    }

    public function testPersonExistsReturnsFalseWhenNotFound()
    {
        // exercise SUT
        $result = $this->personRepository->personExists('Test', 'Guy', 'notanemail@email.com');

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertFalse($result, 'unexpected value');
    }
}