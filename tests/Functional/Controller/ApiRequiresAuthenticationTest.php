<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/8/17
 */

namespace Test\Functional\Controller;


use PapaLocal\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ApiRequiresAuthenticationTest
 *
 * Tests that all api urls are protected authentication.
 *
 * @package Test\Functional\Controller
 */
class ApiRequiresAuthenticationTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testAllEndpointsUnreachableWhenNotAuthenticated($url, $method)
    {
        $this->markTestIncomplete();
	    $client = self::createClient();

        $client->request($method, $url, array(), array(), array('CONTENT_TYPE' => 'application/json'), '{}');

	    $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function urlProvider()
    {
        return [
            ['system/log', 'POST']
        ];
    }
}