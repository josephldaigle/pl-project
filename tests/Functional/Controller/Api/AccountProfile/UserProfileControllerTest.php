<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/8/19
 * Time: 11:49 AM
 */

namespace Test\Functional\Controller\Api\AccountProfile;


use PapaLocal\Core\Security\Cryptographer;
use PapaLocal\Entity\User;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class UserProfileControllerTest
 * @package Test\Functional\Controller\Api\AccountProfile
 */
class UserProfileControllerTest extends WebDatabaseTestCase
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
     * @var string
     */
    private $auth_user = 'lgroom@papalocal.com';

    /**
     * @var Cryptographer
     */
    private $cryptographer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

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
        $this->cryptographer = self::$container->get('PapaLocal\Core\Security\Cryptographer');
        $this->passwordEncoder = self::$container->get('security.user_password_encoder.generic');

        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => $this->auth_user,
            'PHP_AUTH_PW'   => 'eWebify116**',
        ));
        $this->client->followRedirects();
        $this->client->enableProfiler();
    }

    public function testCanUpdateUsername()
    {
        $username = 'guy@tester.com';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user WHERE username ='" . $this->auth_user . "'LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileUsername')->getValue(),
            'emailAddress' => $username
        );

        $this->client->request('POST', '/user/username/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($username, $userRow['username']);
    }

    public function testCanUpdatePassword()
    {
        $password = 'PapaLocal454!!';

        $rowBeforeChange = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user WHERE username ='" . $this->auth_user . "'")
            ->getRow(0);

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfilePassword')->getValue(),
            'password' => $password,
            'confirmPassword' => $password
        );

        $this->client->request('POST', '/user/password/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $rowAfterChange = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user WHERE username ='" . $this->auth_user . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNotSame($rowBeforeChange['password'], $rowAfterChange['password']);
    }

    public function testCanUpdatePersonFirstName()
    {
        $firstName = 'guy';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileFirstName')->getValue(),
            'userGuid' => $userGuid,
            'firstName' => $firstName
        );

        $this->client->request('POST', '/user/first-name/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($firstName, $userRow['firstName']);
    }

    public function testCanUpdatePersonLastName()
    {
        $lastName = 'guy';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileLastName')->getValue(),
            'userGuid' => $userGuid,
            'lastName' => $lastName
        );

        $this->client->request('POST', '/user/last-name/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($lastName, $userRow['lastName']);
    }

    public function testCanUpdatePersonPhoneNumber()
    {
        $phoneNumber = '7748764536';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfilePhoneNumber')->getValue(),
            'phoneNumber' => $phoneNumber,
            'phoneType' => 'MAIN',
            'userGuid' => $userGuid
        );

        $this->client->request('POST', '/user/phone-number/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user_phone WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($phoneNumber, $userRow['phoneNumber']);
    }

    public function testCanUpdatePersonAddress()
    {
        $streetAddress = '8910 Cherry Lane';
        $city = 'Laurel';
        $state = 'Maryland';
        $postalCode = '20708';
        $country = 'United States';
        $type = 'Physical';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileAddress')->getValue(),
            'streetAddress' => $streetAddress,
            'city' => $city,
            'state' => $state,
            'postalCode' => $postalCode,
            'country' => $country,
            'type' => $type,
            'guid' => $userGuid
        );

        $this->client->request('POST', '/user/address/save', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user_address WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($streetAddress, $userRow['streetAddress']);
        $this->assertSame($city, $userRow['city']);
        $this->assertSame($state, $userRow['state']);
        $this->assertSame($postalCode, $userRow['postalCode']);
        $this->assertSame($country, $userRow['country']);
        $this->assertSame($type, $userRow['type']);
    }

}