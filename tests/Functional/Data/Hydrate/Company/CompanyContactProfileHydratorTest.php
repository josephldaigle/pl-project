<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/8/18
 * Time: 3:14 PM
 */


namespace Test\Functional\Data\Hydrate\Company;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Hydrate\Company\CompanyContactProfileHydrator;
use PapaLocal\Entity\Company;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CompanyContactProfileHydratorTest.
 *
 * @package Test\Functional\Data\Hydrate\Company
 */
class CompanyContactProfileHydratorTest extends WebDatabaseTestCase
{
    /**
     * @var CompanyContactProfileHydrator
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
            ->get('PapaLocal\Data\Hydrate\Company\CompanyContactProfileHydrator');
    }

    public function testHydrateAddressListReturnCorrectAddressOnSuccess()
    {
        // set up fixtures
        $addrArr = $this->getConnection()
            ->createQueryTable('addr_arr', 'SELECT * FROM v_company_address WHERE type LIKE \'Physical\' LIMIT 1')
            ->getRow(0);

        $company = (new Company())
            ->setId($addrArr['companyId']);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrateAddressList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertGreaterThan(0, $result->getContactProfile()->getAddressList()->count(),
            'unexpected address list count.');
        $this->assertSame($addrArr['streetAddress'],
            $result->getContactProfile()->findAddressBy('type', AttrType::ADDRESS_PHYSICAL)->getStreetAddress(),
            'address value validation failed');
    }

    public function testHydrateAddressListReturnsEmptyAddressListWhenNoneFound()
    {
        // set up fixtures
        $company = (new Company())
            ->setId(-1);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrateAddressList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertSame(0, $result->getContactProfile()->getAddressList()->count(),
            'unexpected address list count.');
    }

    public function testHydratePhoneNumberListReturnCorrectPhoneNumberOnSuccess()
    {
        // set up fixtures
        $phoneArr = $this->getConnection()
            ->createQueryTable('phone_arr', 'SELECT * FROM v_company_phone WHERE type LIKE \'Business\' LIMIT 1')
            ->getRow(0);

        $company = (new Company())
            ->setId($phoneArr['companyId']);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydratePhoneNumberList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertGreaterThan(0, $result->getContactProfile()->getPhoneNumberList()->count(),
            'unexpected phone list count.');
        $this->assertSame($phoneArr['phoneNumber'],
            $result->getContactProfile()->findPhoneNumberBy('type', AttrType::PHONE_BUSINESS)->getPhoneNumber(),
            'phone value validation failed');
    }

    public function testHydratePhoneNumberListReturnsEmptyPhoneNumberListWhenNoneFound()
    {
        // set up fixtures
        $company = (new Company())
            ->setId(-1);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydratePhoneNumberList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertSame(0, $result->getContactProfile()->getPhoneNumberList()->count(),
            'unexpected phone list count.');
    }

    public function testHydrateEmailAddressListReturnCorrectEmailAddressOnSuccess()
    {
        // set up fixtures
        $emailArr = $this->getConnection()
            ->createQueryTable('email_arr', 'SELECT * FROM v_company_email WHERE type LIKE \'Business\' LIMIT 1')
            ->getRow(0);

        $company = (new Company())
            ->setId($emailArr['companyId']);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrateEmailAddressList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertGreaterThan(0, $result->getContactProfile()->getEmailAddresslist()->count(),
            'unexpected email list count.');
        $this->assertSame($emailArr['emailAddress'],
            $result->getContactProfile()->findEmailAddressBy('type', AttrType::EMAIL_BUSINESS)->getEmailAddress(),
            'email value validation failed');
    }

    public function testHydrateEmailAddressListReturnsEmptyEmailAddressListWhenNoneFound()
    {
        // set up fixtures
        $company = (new Company())
            ->setId(-1);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrateEmailAddressList();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');
        $this->assertSame(0, $result->getContactProfile()->getEmailAddressList()->count(),
            'unexpected email list count.');
    }

    public function testHydrateReturnsCorrectProfileWhenRecordsFound()
    {
        // set up fixtures
        // fetch a companyId from test data
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
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');

        $this->assertGreaterThan(0, $result->getContactProfile()->getAddressList()->count(),
            'unexpected address list count.');
        $this->assertGreaterThan(0, $result->getContactProfile()->getPhoneNumberList()->count(),
            'unexpected phone list count.');
        $this->assertGreaterThan(0, $result->getContactProfile()->getEmailAddressList()->count(),
            'unexpected email list count.');
    }

    public function testHydrateReturnsEmptyContactProfileWhenNoContactDetailsFound()
    {
        // set up fixtures
        $company = (new Company())
            ->setId(-1);

        // exercise SUT
        $this->hydrator->setEntity($company);
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('contactProfile', $result, 'contact profile not found');

        $this->assertEquals(0, $result->getContactProfile()->getAddressList()->count(),
            'unexpected address list count.');
        $this->assertEquals(0, $result->getContactProfile()->getPhoneNumberList()->count(),
            'unexpected phone list count.');
        $this->assertEquals(0, $result->getContactProfile()->getEmailAddressList()->count(),
            'unexpected email list count.');
    }
}