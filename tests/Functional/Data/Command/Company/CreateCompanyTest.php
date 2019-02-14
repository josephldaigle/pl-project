<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/9/18
 * Time: 1:18 PM
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Data\Command\Company\CreateCompany;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Company;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateCompanyTest.
 *
 * @package Test\Functional\Data\Command\Company
 */
class CreateCompanyTest extends WebDatabaseTestCase
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
		    'Person',
		    'User',
		    'L_UserRole',
		    'R_UserCompanyRole'
	    ]);

        parent::setUp();

        //set up fixtures
        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateCompanyReturnsCompanyWithIdSetOnSuccess()
    {
        // set up fixtures
        $begCompCount = $this->getConnection()->getRowCount('Company');
        $begRUserCompRoleCount = $this->getConnection()->getRowCount('R_UserCompanyRole');

        $userId = intval($this->getConnection()
            ->createQueryTable('user_id', 'SELECT MAX(id) as \'id\' FROM User')
            ->getRow(0)['id']);

        $company = (new Company())
            ->setName('Test Company Name, LLC');

        //exercise SUT
        $command = new CreateCompany($userId, $company);
        $result = $this->persistence->execute($command);

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected return type');
        $this->assertObjectHasAttribute('id', $result, 'id property missing');
        $this->assertFalse(is_null($result->getId()), 'id property contains unexpected value');
        $this->assertTableRowCount('Company', $begCompCount + 1, 'Company table not incremented');
        $this->assertTableRowCount('R_UserCompanyRole', $begRUserCompRoleCount + 1,
            'R_UserCompanyRole table not incremented');
    }
}