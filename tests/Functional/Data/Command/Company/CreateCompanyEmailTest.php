<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 5:34 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Company\CreateCompanyEmail;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateCompanyEmailTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class CreateCompanyEmailTest extends WebDatabaseTestCase
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

        //set up fixtures
        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateCompanyEmailReturnsEmailAddressOnSuccess()
    {
        // set up fixtures
        $begEmailCount = $this->getConnection()->getRowCount('EmailAddress');
        $begCoEmailCount = $this->getConnection()->getRowCount('R_CompanyEmailAddress');

        $emailAddress = (new EmailAddress())
            ->setEmailAddress('notexistingemail@papalocal.com')
            ->setType(AttrType::EMAIL_BUSINESS);

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT id FROM Company WHERE id NOT IN (SELECT companyId FROM R_CompanyEmailAddress)')
            ->getRow(0)['id']);

        //exercise SUT
        $createEmailCmd = new CreateCompanyEmail($companyId, $emailAddress);
        $result = $this->persistence->execute($createEmailCmd);

        // make assertions
        $this->assertTableRowCount('EmailAddress', $begEmailCount + 1, 'EmailAddress table not incremented');
        $this->assertTableRowCount('R_CompanyEmailAddress', $begCoEmailCount + 1, 'R_CompanyEmailAddress table not incremented');
        $this->assertInstanceOf(EmailAddress::class, $result, 'unexpected instance type');
        $this->assertObjectHasAttribute('id', $result);
        $this->assertFalse(is_null($result->getId()));
    }
}