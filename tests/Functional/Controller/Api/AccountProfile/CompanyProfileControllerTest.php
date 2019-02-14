<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/8/19
 * Time: 10:13 AM
 */

namespace Test\Functional\Controller\Api\AccountProfile;


use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class CompanyProfileControllerTest
 * @package Test\Functional\Controller\Api\AccountProfile
 */
class CompanyProfileControllerTest extends WebDatabaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CsrfTokenManager
     */
    private $csrfTokenManager;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([]);

        parent::setUp();

        // create an authenticated client
        self::bootKernel();

        $this->csrfTokenManager = self::$container->get('security.csrf.token_manager');

        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'lgroom@papalocal.com',
            'PHP_AUTH_PW'   => 'eWebify116**',
        ));
        $this->client->followRedirects();
        $this->client->enableProfiler();
    }

    public function testCanCreateCompany()
    {
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('v_company');

        $companyName = 'papalocal';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addCompany')->getValue(),
            'name' => $companyName,
            'phoneNumber' => '8787899889',
            'emailAddress' => 'guy@tester.com',
            'address' => array(
                "streetAddress" => "349 Georgia Avenue",
                "city" => "Silver Spring",
                "state" => "Maryland",
                "postalCode" => "20902",
                "country" => "United States"
            ),
        );

        $this->client->request('POST', '/company/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $companyRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_company WHERE name ='" . $companyName . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('v_company', $begRowCount + 1, 'Company table row count');
        $this->assertSame($companyName, $companyRow['name']);
    }

    public function testCanUpdateCompanyEmailAddress()
    {
        $emailAddress = 'guy@tester.com';

        $companyGuid = $this->getConnection()
            ->createQueryTable('companyGuid', "SELECT companyGuid FROM v_company_email_address LIMIT 1")
            ->getRow(0)['companyGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileEmail')->getValue(),
            'guid' => $companyGuid,
            'emailAddress' => $emailAddress,
            'type' => 'Business'
        );

        $this->client->request('POST', '/company/email/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $companyRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_company_email_address WHERE companyGuid ='" . $companyGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($emailAddress, $companyRow['emailAddress']);
    }

    public function testCanUpdateCompanyPhoneNumber()
    {
        $phoneNumber = '7708964536';

        $companyGuid = $this->getConnection()
            ->createQueryTable('companyGuid', "SELECT companyGuid FROM v_company_phone_number LIMIT 1")
            ->getRow(0)['companyGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfilePhoneNumber')->getValue(),
            'guid' => $companyGuid,
            'phoneNumber' => $phoneNumber,
            'type' => 'Business'
        );

        $this->client->request('POST', '/company/phone-number/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $companyRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_company_phone_number WHERE companyGuid ='" . $companyGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($phoneNumber, $companyRow['phoneNumber']);
    }

    public function testCanUpdateCompanyWebsite()
    {
        $website = 'papalocal.com';

        $companyGuid = $this->getConnection()
            ->createQueryTable('companyGuid', "SELECT guid FROM v_company LIMIT 1")
            ->getRow(0)['guid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileWebsite')->getValue(),
            'guid' => $companyGuid,
            'website' => $website,
            'type' => 'Business'
        );

        $this->client->request('POST', '/company/website/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $companyRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_company WHERE guid ='" . $companyGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($website, $companyRow['website']);
    }
}