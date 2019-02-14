<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/8/18
 * Time: 8:38 PM
 */


namespace Test\Functional\Data\Hydrate\Company;


use PapaLocal\Data\Hydrate\Company\CompanyHydrator;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CompanyHydratorTest.
 *
 * @package Test\Functional\Data\Hydrate\Company
 */
class CompanyHydratorTest extends WebDatabaseTestCase
{
    /**
     * @var CompanyHydrator
     */
    private $hydrator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Company',
		    'L_AddressType',
		    'Address',
		    'R_CompanyAddress',
		    'L_EmailAddressType',
		    'EmailAddress',
		    'R_CompanyEmailAddress',
		    'L_PhoneNumberType',
		    'PhoneNumber',
		    'R_CompanyPhoneNumber'
	    ]);

        parent::setUp();

        $this->hydrator = $this->diContainer
            ->get('PapaLocal\Data\Hydrate\Company\CompanyHydrator');
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @expectedExceptionMessageRegExp /^(Unable to find a matching company)/
     */
    public function testHydrateThrowsExceptionWhenNoRecordFound()
    {
        // set up fixtures
        $company = (new Company())
            ->setId(-1);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $this->hydrator->hydrate();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(Entity supplied must have an id assigned)/
     */
    public function testHydrateThrowsExceptionWhenCompanyIdNotSupplied()
    {
        // set up fixtures
        $company = (new Company());

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrate();

    }

    public function testHydrateReturnsCompanyWhenFound()
    {
        // set up fixtures
        $companyArr = $this->getConnection()
            ->createQueryTable('company_arr', 'SELECT * FROM Company LIMIT 1')
            ->getRow(0);

        $company = (new Company())
            ->setId($companyArr['id']);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame($companyArr['id'], $result->getId(), 'id validation failed');
        $this->assertSame($companyArr['name'], $result->getName(), 'name validation failed');
        $this->assertSame($companyArr['timeCreated'], $result->getTimeCreated(),
            'timeCreated validation failed');
        $this->assertSame($companyArr['dateFounded'], $result->getDateFounded(),
            'dateFounded validation failed');
    }

    public function testHydrateReturnsCompanyWithContactProfileWhenCascadeIsTrue()
    {
        // set up fixtures
        $companyId = intval($this->getConnection()
            ->createQueryTable('cid',
                'SELECT id FROM Company WHERE id IN (SELECT companyId FROM v_company_address)
                AND id IN (SELECT companyId FROM v_company_phone)
                AND id IN (SELECT companyId FROM v_company_email)')
            ->getRow(0)['id']);

        $company = (new Company())
            ->setId($companyId);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrate(true);

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertGreaterThan(0, $result->getContactProfile()->getAddressList()->count(),
            'address list validation failed');
        $this->assertGreaterThan(0, $result->getContactProfile()->getPhoneNumberList()->count(),
            'phone list validation failed');
        $this->assertGreaterThan(0, $result->getContactProfile()->getEmailAddressList()->count(),
            'email list validation failed');

    }
}