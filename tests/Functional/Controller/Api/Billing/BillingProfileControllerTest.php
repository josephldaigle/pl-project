<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/9/19
 * Time: 7:34 AM
 */

namespace Test\Functional\Controller\Api\Billing;


use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class BillingProfileControllerTest
 * @package Test\Functional\Controller\Api\Billing
 */
class BillingProfileControllerTest extends WebDatabaseTestCase
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

    public function testCanSaveRechargeSettings()
    {
        $maxBalance = '250.00';
        $minBalance = '50.00';

        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', "SELECT userGuid FROM v_user_billing_profile LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('accountProfileRecharge')->getValue(),
            'maxBalance' => $maxBalance,
            'minBalance' => $minBalance,
            'userGuid' => $userGuid,
        );

        $this->client->request('POST', '/billing/account/recharge-setting', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $userRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_user_billing_profile WHERE userGuid ='" . $userGuid . "'")
            ->getRow(0);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame($maxBalance, $userRow['maxBalance']);
        $this->assertSame($minBalance, $userRow['minBalance']);
    }
}