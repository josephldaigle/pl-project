<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/5/17
 * Time: 7:04 PM
 */

namespace Test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityControllerTest.
 *
 * Tests HTTP responses from SecurityController.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->client->followRedirects();
    }

    public function testCanAccessLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $loginForm = $crawler->filter('form[name="login"]');

        $this->assertCount(1, $loginForm);
    }

    public function testLoginFormExposesRegistrationForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $registerForm = $crawler->filter('form[name="userRegistration"]');

        $this->assertCount(1, $registerForm);
    }

    /**
     * @dataProvider registerReturnsMethodNotAllowedWhenNotPostProvider
     */
    public function testRegisterReturnsMethodNotAllowedWhenNotPost($method)
    {
        $this->client->request($method, '/register');

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function registerReturnsMethodNotAllowedWhenNotPostProvider()
    {
        return [
            ['GET'],
            ['PUT'],
            ['DELETE'],
            ['HEAD'],
            ['CONNECT'],
            ['OPTIONS'],
            ['TRACE'],
            ['PATCH']
        ];
    }
}