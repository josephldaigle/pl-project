<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/20/19
 * Time: 10:13 PM
 */

namespace Test\Functional\Controller\Api\Billing;


use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class TransactionControllerTest
 * @package Test\Functional\Controller\Api\Billing
 */
class TransactionControllerTest extends WebDatabaseTestCase
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

    public function testCanPayoutUser()
    {
        $this->markTestIncomplete();
        
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('JournalSuccess');

        $userGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT userGuid FROM v_user WHERE username='lgroom@papalocal.com' LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('withdrawFunds')->getValue(),
            'userGuid' => $userGuid,
            'amount' => '90',
            'address' => "349 Georgia Avenue, Silver Spring, Maryland, 20902, United States",
            'deliveryMethod' => 'check'
        );

        $this->client->request('POST', '/billing/transaction/withdraw', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('JournalSuccess', $begRowCount + 1, 'JournalSuccess table row count');
    }

    public function testPayoutUserFailsOnExcessiveWithdrawalAmountException()
    {
        // set up fixtures
        $userGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT userGuid FROM v_user WHERE username='lgroom@papalocal.com' LIMIT 1")
            ->getRow(0)['userGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('withdrawFunds')->getValue(),
            'userGuid' => $userGuid,
            'amount' => '90',
            'address' => "349 Georgia Avenue, Silver Spring, Maryland, 20902, United States",
            'deliveryMethod' => 'check'
        );

        $this->client->request('POST', '/billing/transaction/withdraw', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}