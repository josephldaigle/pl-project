<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/23/18
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\Command\Person\PersonHasEmail;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class PersonHasEmailTest.
 *
 * TODO: Refactor into repository
 *
 * @package Test\Functional\Data\Command\Person
 */
class PersonHasEmailTest extends WebDatabaseTestCase
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
		    'EmailAddress',
		    'L_EmailAddressType',
		    'R_PersonEmailAddress'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testPersonHasEmailReturnsFalseWhenNoneFound()
    {
        // set up fixtures
        $personId = $this->getConnection()
            ->createQueryTable('pers_id', 'SELECT id FROM Person LIMIT 1')
            ->getRow(0)['id'];

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('nonExisting@email.com');

        // exercise SUT
        $hasEmailCmd = new PersonHasEmail($personId, $emailAddress);
        $result = $this->persistence->execute($hasEmailCmd);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');

    }

    public function testPersonHasEmailReturnsTrueOnSuccess()
    {
        // set up fixtures
        $persEmailRow = $this->getConnection()
            ->createQueryTable('pers_email',
                'SELECT * FROM R_PersonEmailAddress LIMIT 1')
            ->getRow(0);
        $emailRow = $this->getConnection()
            ->createQueryTable('email',
                'SELECT * FROM EmailAddress  WHERE id = ' . $persEmailRow['emailId'])
            ->getRow(0);

        $emailAddress = (new EmailAddress())
            ->setEmailAddress($emailRow['emailAddress']);

        //exercise SUT
        $hasEmailCmd = new PersonHasEmail($persEmailRow['personId'], $emailAddress);
        $result = $this->persistence->execute($hasEmailCmd);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');
    }
}