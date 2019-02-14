<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/28/17
 */

namespace Test\Functional\Controller\Api\Billing;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class PaymentMethodControllerTest.
 *
 */
class PaymentMethodControllerTest extends WebTestCase
{
    /**
     * @var Client client
     */
    private $client;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'test@ewebify.com',
            'PHP_AUTH_PW'   => 'testUser123!!',
        ));
        $this->client->followRedirects();
    }

    //TODO: Finish implementing
    public function testSaveBankAccountConvertsParamsCorrectly()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        //set up fixtures
        $json = json_encode(array(
            'accountNumber' => '123123123123123123123',
            'accountType' => 'checking',
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'bankName' => 'Bank of America',
            'routingNumber' => '123123123',
        ));

        //exercise SUT
        $this->client->request('POST','billing', array(), array(), array('CONTENT_TYPE' => 'application/json'), $json);

        //fetch response in to assoc array
        $response = json_decode($this->client->getResponse()->getContent(), true);

        //make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'unexpected response code');

        //assert response expectations
        $this->assertTrue(is_array($response), 'response not array');
        $this->assertArrayHasKey('message', $response, '[message] index missing from response');
        $this->assertContains('success', $response, 'missing expected message');
    }
}