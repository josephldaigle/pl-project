<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/11/18
 * Time: 4:30 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Company\UpdateEmailAddress;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * UpdateEmailAddressTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class UpdateEmailAddressTest extends WebDatabaseTestCase
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
    		'Company',
		    'EmailAddress',
		    'L_EmailAddressType',
		    'R_CompanyEmailAddress'
	    ]);

    	parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');

    }

    /**
     * When company does not have phone type, use CreatePhoneNumber command
     * @expectedException PapaLocal\Entity\Exception\QueryCommandFailedException
     * @expectedExceptionMessageRegExp /^(Unexpected row count returned)/
     */
    public function testUpdateEmailAddressThrowsExceptionWhenCompanyDoesNotHaveEmailType()
    {
        // set up fixtures
        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyEmailAddress LIMIT 1')
            ->getRow(0)['companyId']);

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('test@papalocal.com')
            ->setType(AttrType::EMAIL_OTHER);

        // exercise SUT
        $cmd = new UpdateEmailAddress($companyId, $emailAddress);
        $this->persistence->execute($cmd);
    }

    public function testUpdateEmailAddressIsSuccessWhenEmailAddressNotExists()
    {
        // set up fixtures
        $emailRowCount = $this->getConnection()->getRowCount('EmailAddress');
        $compEmailRowCount = $this->getConnection()->getRowCount('R_CompanyEmailAddress');

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyEmailAddress LIMIT 1')
            ->getRow(0)['companyId']);

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('test@papalocal.com')
            ->setType(AttrType::EMAIL_BUSINESS);

        // exercise SUT
        $cmd = new UpdateEmailAddress($companyId, $emailAddress);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
        $this->assertTableRowCount('EmailAddress', $emailRowCount + 1,
            'EmailAddress table not incremented');
        $this->assertTableRowCount('R_CompanyEmailAddress', $compEmailRowCount,
            'unexpected row count in R_CompanyEmailAddress');
    }

    public function testUpdateEmailAddressIsSuccessWhenEmailAddressExists()
    {
        // set up fixtures
        $emailRowCount = $this->getConnection()->getRowCount('EmailAddress');
        $compEmailRowCount = $this->getConnection()->getRowCount('R_CompanyEmailAddress');

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT companyId FROM R_CompanyEmailAddress LIMIT 1')
            ->getRow(0)['companyId']);

        $emailArr = $this->getConnection()
            ->createQueryTable('email_address', 'SELECT * FROM EmailAddress LIMIT 1')
            ->getRow(0);

        $emailAddress = (new EmailAddress())
            ->setEmailAddress($emailArr['emailAddress'])
            ->setType(AttrType::EMAIL_BUSINESS);

        // exercise SUT
        $cmd = new UpdateEmailAddress($companyId, $emailAddress);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
        $this->assertTableRowCount('EmailAddress', $emailRowCount,
            'EmailAddress table not incremented');
        $this->assertTableRowCount('R_CompanyEmailAddress', $compEmailRowCount,
            'unexpected row count in R_CompanyEmailAddress');
    }
}