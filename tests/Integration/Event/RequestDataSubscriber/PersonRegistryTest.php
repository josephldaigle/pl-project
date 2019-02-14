<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/2/18
 */


namespace Test\Integration\Event\RequestDataSubscriber;


use PapaLocal\Data\Repository\Person\PersonRepository;
use PapaLocal\Entity\Person;
use PapaLocal\Entity\Registry\PersonRegistry;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class PersonRegistryTest.
 *
 * Tests that the PersonRegistry can be loaded and utilized properly.
 *
 * @package Test\Integration\Event\RequestDataSubscriber
 */
class PersonRegistryTest extends WebDatabaseTestCase
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
            'Person',
            'User',
            'L_EmailAddressType',
            'EmailAddress',
            'R_PersonEmailAddress'
        ]);

        parent::setUp();

        $this->personRepository = $this->diContainer->get('PapaLocal\Data\Repository\Person\PersonRepository');
    }

    public function testCanLoadPersonRegistryFromDatabase()
    {
        // set up fixtures

        // exercise SUT
        $registry = $this->personRepository->loadAll();

        // make assertions
        $this->assertInstanceOf(PersonRegistry::class, $registry, 'unexpected type');
        $this->assertNotNull($registry, 'return value is null');
    }

    public function testPersonExists()
    {
        // set up fixtures
        $personRow = $this->getConnection()
            ->createQueryTable('person', 'SELECT * FROM v_user LIMIT 1')
            ->getRow(0);

        $registry = $this->personRepository->loadAll();

        // exercise SUT
        $result = $registry->personExists($personRow['firstName'], $personRow['lastName'], $personRow['username']);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals($result, $personRow['personId'], 'unexpected value');
    }

    public function testFetchById()
    {
        // set up fixtures
        $personRow = $this->getConnection()
            ->createQueryTable('person', 'SELECT * FROM v_user LIMIT 1')
            ->getRow(0);

        $registry = $this->personRepository->loadAll();

        // exercise SUT
        $result = $registry->fetchById($personRow['personId']);

        // make assertions
        $this->assertInstanceOf(Person::class, $result, 'unexpected type');
        $this->assertEquals($personRow['personId'], $result->getId(), 'unexpected value');
    }
}