<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/25/18
 * Time: 7:07 AM
 */

namespace Test\Functional\IdentityAccess;


use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class UserAccountTest
 *
 * @package Test\Functional\IdentityAccess
 */
class UserAccountTest extends WebDatabaseTestCase
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
        $this->configureDataSet();

        parent::setUp();

        // create an authenticated client
        self::bootKernel();

        $this->client = self::createClient();

        $this->client->followRedirects();
        $this->client->enableProfiler();

        $this->csrfTokenManager = self::$container->get('security.csrf.token_manager');
    }

    public function testCanRegisterUserWithoutCompany()
    {
        // set up fixtures
        $begUserRowCount = $this->getConnection()->getRowCount('v_user');

        $postData = array(
            '_csrf_token' => $this->csrfTokenManager->getToken('userRegistration')->getValue(),
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'username' => 'gtester@papalocal.com',
            'phoneNumber' => 2224445555,
            'password' => 'Som3P@$$w0rd1',
            'confirmPassword' => 'Som3P@$$w0rd1'
        );

        // exercise SUT
        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('v_user', $begUserRowCount + 1, 'v_user table row count');
    }

    public function testCanRegisterUserWithCompany()
    {
        // set up fixtures
        $begUserRowCount = $this->getConnection()->getRowCount('v_user');
        $begCoRowCount = $this->getConnection()->getRowCount('Company');

        $postData = array(
            '_csrf_token' => $this->csrfTokenManager->getToken('userRegistration')->getValue(),
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'username' => 'gtester@papalocal.com',
            'phoneNumber' => 2224445555,
            'password' => 'Som3P@$$w0rd1',
            'confirmPassword' => 'Som3P@$$w0rd1',
            'companyName' => 'Guys Company',
            'companyEmailAddress' => 'gtester@papalocal.com',
            'companyPhoneNumber' => 2224445555,
            'companyAddress' => array(
                'streetAddress' => '131 W James Cir.',
                'city' => 'Hampton',
                'state' => 'GA',
                'postalCode' => 30228,
                'country' => 'United States'
            )
        );

        // exercise SUT
        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('v_user', $begUserRowCount + 1, 'v_user table row count');
        $this->assertTableRowCount('Company', $begCoRowCount + 1, 'company table row count');
    }
}