<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/2/18
 */

namespace PapaLocal\Test;


use PapaLocal\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * Trait AuthenticatedTestTrait
 *
 * @package PapaLocal\Test
 */
trait AuthenticatedTestTrait
{
    /**
     * Authenticates the client using a token, so that pages can be tested.
     *
     * @param Client $client
     * @return Client
     */
    public function login(Client $client)
    {
        $session = $this->client->getContainer()->get('session');

        $user = new User();
        $user->setUsername('lgroom@papalocal.com');
        $user->setPassword('eWebify116**');

        $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_ADMIN'));
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        return $client;
    }
}