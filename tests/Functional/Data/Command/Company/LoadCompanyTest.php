<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/6/18
 */


namespace Test\Functional\Data\Command\Company;


use PapaLocal\Data\Command\Company\LoadCompany;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Company;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * LoadCompanyTest.
 *
 * TODO: Refactor into repository
 *
 * @package Test\Functional\Data\Command\Company
 */
class LoadCompanyTest extends WebDatabaseTestCase
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
    		'Company'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testLoadCompanyReturnsCompanyOnSuccess()
    {
        // set up fixtures
        $companyId = intval($this->getConnection()
            ->createQueryTable('company', 'SELECT MAX(id) as \'id\' FROM Company')
            ->getRow(0)['id']);

        // exercise SUT
        $result = $this->persistence->execute(new LoadCompany($companyId));

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('name', $result, 'name property not found');
        $this->assertNotNull($result->getName(), 'name property is null');
    }

}