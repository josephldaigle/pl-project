<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 8:31 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Company\CreateCompanyPhone;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateCompanyPhoneTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class CreateCompanyPhoneTest extends WebDatabaseTestCase
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
		    'PhoneNumber',
		    'L_PhoneNumberType',
		    'R_CompanyPhoneNumber',
	    ]);

        parent::setUp();

        //set up fixtures
        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateCompanyPhoneReturnsPhoneNumberOnSuccess()
    {
        // set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('5555555555')
            ->setType(AttrType::PHONE_MAIN);

        $begPhoneCount = $this->getConnection()->getRowCount('PhoneNumber');
        $begCoPhoneCount = $this->getConnection()->getRowCount('R_CompanyPhoneNumber');

        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT MAX(id) as \'id\' FROM Company')
            ->getRow(0)['id']);

        //exercise SUT
        $createEmailCmd = new CreateCompanyPhone($companyId, $phoneNumber);
        $result = $this->persistence->execute($createEmailCmd);

        // make assertions
        $this->assertTableRowCount('PhoneNumber', $begPhoneCount + 1, 'PhoneNumber table not incremented');
        $this->assertTableRowCount('R_CompanyPhoneNumber', $begCoPhoneCount + 1, 'R_CompanyPhoneNumber table not incremented');
        $this->assertInstanceOf(PhoneNumber::class, $result, 'unexpected instance type');
        $this->assertObjectHasAttribute('id', $result);
        $this->assertFalse(is_null($result->getId()));
    }
}