<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/8/17
 * Time: 7:33 PM
 */


namespace Test\Functional\Controller\Api;


use PapaLocal\Entity\LogStatement;
use PapaLocal\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class SystemControllerTest
 *
 * @package Test\Functional\Controller\Api
 */
class SystemControllerTest extends WebTestCase
{
    /**
     * @dataProvider logActionReturnsBadMethodStatusWhenRequestTypeNotPostProvider
     */
    public function testLogActionReturnsBadMethodStatusWhenRequestTypeNotPost($method)
    {
        $this->client->request($method, 'system/log', array(
            'level' => LogStatement::INFO,
            'message' => 'This is logAction test.'
        ));

        //make assertions
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Omits:
     *  POST
     *
     * @return array
     */
    public function logActionReturnsBadMethodStatusWhenRequestTypeNotPostProvider()
    {
        return [
            'GET' => ['GET'],
            'PUT' => ['PUT'],
            'DELETE' => ['DELETE'],
            'HEAD' => ['HEAD'],
            'CONNECT' => ['CONNECT'],
            'OPTIONS' => ['OPTIONS'],
            'TRACE' => ['TRACE'],
            'PATCH' => ['PATCH']
        ];
    }

    /**
     * Also tests that the SUT returns a 400 response code
     */
    public function testLogActionReturnsValidationErrorsWhenRequestDoesNotContainData()
    {
    	$this->markTestIncomplete();
        //set up fixtures
        $json = json_encode(array());

        //exercise SUT
        $this->client->request('POST','system/log', array(), array(), array('CONTENT_TYPE' => 'application/json'), $json);

        //fetch response in to assoc array
        $response = json_decode($this->client->getResponse()->getContent(), true);

        //make assertions
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), 'unexpected response code');

        //assert response expectations
        $this->assertTrue(is_array($response), 'response not array');
        $this->assertArrayHasKey('validationErrors', $response, 'validationErrors missing from response');
        $this->assertContains('Level must be present', $response['validationErrors'][0], 'missing validation error for [level] field');
        $this->assertContains('Message must contain a value.', $response['validationErrors'][1], 'missing validation error for [message] field');

    }

    /**
     * @dataProvider controllerRedirectsToLoginWhenNotAuthenticatedProvider
     *
     * @param $method   HTTP method to call on $url
     * @param $url      the url to test
     */
    public function testControllerRedirectsToLoginWhenNotAuthenticated($method, $url)
    {
        $this->markTestIncomplete();
	    //logout, and turn off follow redirects
        $unauthClient = self::createClient();

        //perform test
        $unauthClient->request($method, $url);

        //make assertions
        $this->assertEquals(302, $unauthClient->getResponse()->getStatusCode(), 'unexpected status code');
        $this->assertInstanceOf(RedirectResponse::class, $unauthClient->getResponse(), 'unexpected response type');
        $this->assertRegExp('/(login)$/', $unauthClient->getResponse()->getTargetUrl(), 'unexpected target url');
    }

    /**
     * Data provider
     * @return array ['HTTP METHOD', 'url']
     */
    public function controllerRedirectsToLoginWhenNotAuthenticatedProvider()
    {
        return [
            ['POST', '/system/log'],
        ];
    }


    public function testLogActionReturnsCorrectResponseOnSuccess()
    {
	    $this->markTestIncomplete();

	    //set up fixtures
        $json = json_encode(array(
            'level' => LogStatement::INFO,
            'message' => 'This is logAction test.'
        ));

        //exercise SUT
        $this->client->request('POST','api/system/log', array(), array(), array('CONTENT_TYPE' => 'application/json'), $json);

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