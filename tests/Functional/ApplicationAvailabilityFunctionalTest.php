<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/19/17
 * Time: 6:25 PM
 */

namespace Test\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApplicationAvailabilityFunctionalTest.
 *
 * Tests that the applications routes all return a status code
 * between 200 and 299.
 *
 * @see https://symfony.com/doc/current/best_practices/tests.html
 *      https://symfony.com/doc/current/testing.html
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'test@ewebify.com',
            'PHP_AUTH_PW'   => 'testUser123!!',
        ));
        $this->client->followRedirects();
    }

    /**
     * Only tests pages that page urls respond to requests, not that the request is
     * accepted or honored.
     *
     * REST endpoints are tested separately.
     *
     * @dataProvider pageUrlProvider
     */
    public function testPublicPagesAreAccessible($url)
    {

        $this->markTestSkipped('Pending test env authentication fix.');
        // exercise SUT
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // make assertions
        $this->assertNotSame(404, $response->getStatusCode(),
            'Unexpected status code: ' . $response->getStatusCode());

    }

    public function pageUrlProvider()
    {
        return array(
            array('/login'),
            array('/feed'),
            array('/terms-of-service')
        );
    }
}