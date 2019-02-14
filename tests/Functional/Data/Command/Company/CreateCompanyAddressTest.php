<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/9/18
 * Time: 5:03 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Core\ValueObject\AddressType;
use PapaLocal\Data\Command\Company\CreateCompanyAddress;
use PapaLocal\Data\DataService;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateCompanyAddressTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class CreateCompanyAddressTest extends WebDatabaseTestCase
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
		    'Address',
		    'L_AddressType',
		    'R_CompanyAddress'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');

    }

    public function testCreateCompanyAddressReturnsAddressOnSuccess()
    {
        // set up fixtures
        $companyId = intval($this->getConnection()
            ->createQueryTable('cid', 'SELECT MAX(id) as \'id\' FROM Company LIMIT 1')
            ->getRow(0)['id']);

        $begAddrRowCount = $this->getConnection()->getRowCount('Address');
        $begRCoAddrRowCount = $this->getConnection()->getRowCount('R_CompanyAddress');

        $address = (new Address('222 Test Dr.', 'Testville', 'GA', '32690', 'United States', AddressType::PHYSICAL()));

        // exercise SUT
        $createAddressCommand = new CreateCompanyAddress($companyId, $address);
        $this->persistence->execute($createAddressCommand);

        // make assertions
        $this->assertTableRowCount('Address', $begAddrRowCount + 1, 'Address table not incremented');
        $this->assertTableRowCount('R_CompanyAddress', $begRCoAddrRowCount + 1, 'Company table not incremented');

    }
}