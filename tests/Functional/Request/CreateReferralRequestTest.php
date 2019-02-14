<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/2/18
 */


namespace Test\Functional\Request;


use PapaLocal\Test\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CreateReferralRequestTest.
 *
 * Test that the application receives, converts and validates requests to add Referrals.
 *
 * @package Test\Functional\Request
 */
class CreateReferralRequestTest extends AuthenticatedTestCase
{

    public function testCanLogin()
    {
        // test that user can access feed page
        $crawler = $this->client->request('GET', 'feed');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testRequestValidation()
    {
        $this->markTestIncomplete();
        $container = $this->diContainer;
        $tokenManager = $container->get('security.csrf.token_manager');
        $tokenString = $tokenManager->refreshToken('addReferral');

        //set up fixtures
        $json = json_encode(array(
            '_csrf_token' => $tokenString->getValue(),
            'about' => 'short about text',
            'address' => array(
                'streetAddress' => '131 West Paces Ferry Road Northwest',
                'city' => 'Atlanta',
                'state' => 'Georgia',
                'postalCode' => 30305,
                'country' => 'United States',
            )
        ));

        $crawler = $this->client->request('POST','api/referral/add', array(), array(), array('CONTENT_TYPE' => 'application/json'), $json);

//
//        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), 'unexpected http response code');
    }
}