<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/26/18
 * Time: 8:18 AM
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\UpdatePersonEmail;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * UpdatePersonEmailTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class UpdatePersonEmailTest extends WebDatabaseTestCase
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

    public function testUpdatePersonEmailReturnsAffectedRowCountOnSuccess()
    {
        // set up fixtures
        $personId = intval($this->getConnection()
            ->createQueryTable('pers_id', 'SELECT * FROM Person LIMIT 1')
            ->getRow(0)['id']);

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('newEmail@papalocal.com')
            ->setType(AttrType::EMAIL_PERSONAL);

        // exercise SUT
        $updateEmailCmd = new UpdatePersonEmail($personId, $emailAddress);
        $result = $this->persistence->execute($updateEmailCmd);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
    }

}