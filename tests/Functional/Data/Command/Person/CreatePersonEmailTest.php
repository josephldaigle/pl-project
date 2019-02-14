<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/11/18
 * Time: 1:27 PM
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\CreatePersonEmail;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreatePersonEmailTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class CreatePersonEmailTest extends WebDatabaseTestCase
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

    public function testCreatePersonEmailReturnsEmailAddressOnSuccess()
    {
        // set up fixtures
        $begEmailCount = $this->getConnection()->getRowCount('EmailAddress');
        $begPersEmailCount = $this->getConnection()->getRowCount('R_PersonEmailAddress');

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('test@emailaddress.com')
            ->setType(AttrType::EMAIL_BUSINESS);

        $personId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0)['id']);

        //exercise SUT
        $createEmailCmd = new CreatePersonEmail($personId, $emailAddress);
        $result = $this->persistence->execute($createEmailCmd);

        // make assertions
        $this->assertTableRowCount('EmailAddress', $begEmailCount + 1,
            'EmailAddress table not incremented');
        $this->assertTableRowCount('R_PersonEmailAddress', $begPersEmailCount + 1,
            'R_PersonEmailAddress not incremented');
        $this->assertInstanceOf(EmailAddress::class, $result, 'unexpected instance type');
        $this->assertObjectHasAttribute('id', $result);
        $this->assertFalse(is_null($result->getId()));
    }

	public function testCreatePersonEmailReturnsEmailAddressOnSuccessWhenEmailExists()
	{
		// set up fixtures
		$begEmailCount = $this->getConnection()->getRowCount('EmailAddress');
		$begPersEmailCount = $this->getConnection()->getRowCount('R_PersonEmailAddress');

		$emailArr = $this->getConnection()
			->createQueryTable('email_address', 'SELECT emailAddress FROM EmailAddress LIMIT 1')
			->getRow(0);

		$emailAddress = (new EmailAddress())
			->setEmailAddress($emailArr['emailAddress'])
			->setType(AttrType::EMAIL_BUSINESS);

		$personId = intval($this->getConnection()
		                        ->createQueryTable('cid', 'SELECT MAX(id) as \'id\' FROM Person')
		                        ->getRow(0)['id']);

		//exercise SUT
		$createEmailCmd = new CreatePersonEmail($personId, $emailAddress);
		$result = $this->persistence->execute($createEmailCmd);

		// make assertions
		$this->assertTableRowCount('EmailAddress', $begEmailCount,
			'unexpected EmailAddress row count');
		$this->assertTableRowCount('R_PersonEmailAddress', $begPersEmailCount + 1,
			'R_PersonEmailAddress not incremented');
		$this->assertInstanceOf(EmailAddress::class, $result, 'unexpected instance type');
		$this->assertObjectHasAttribute('id', $result);
		$this->assertFalse(is_null($result->getId()));
	}
}