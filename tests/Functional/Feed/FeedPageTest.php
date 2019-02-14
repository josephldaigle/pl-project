<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/7/18
 * Time: 2:32 PM
 */

namespace Test\Functional\Feed;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class FeedPageTest
 *
 * @package Test\Functional\Feed
 */
class FeedPageTest extends WebDatabaseTestCase
{
    /**
     * @var AuthorizeDotNet
     */
    private $authNet;

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
	    $this->configureDataSet([
	        'Person',
            'EmailAddress',
            'L_EmailAddressType',
            'R_PersonEmailAddress',
            'User',
            'L_UserRole',
            'R_UserApplicationRole',
            'R_UserNotification'
        ]);

		parent::setUp();

		$this->authNet = $this->diContainer->get('PapaLocal\AuthorizeDotNet\AuthorizeDotNet');
	}

	/**
	 * TODO: Fix this to actually check against the filter. Currently just checks if the feed card id's are in reverse order.
	 */
	public function testFeedIsOrderedCorrectly()
	{
	    $this->markTestSkipped('Needs to be refactored due to authentication changes since 4.1 upgrade');
		$session = $this->diContainer->get('session');

		$firewallName = 'main';
		// if you don't define multiple connected firewalls, the context defaults to the firewall name
		// See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
		$firewallContext = 'main';

		$user = new User();
		$user->setUsername('lgroom@papalocal.com');
		$user->setPassword('eWebify116**');

		$token = new UsernamePasswordToken($user, null, $firewallName, array('ROLE_ADMIN'));
		$session->set('_security_' . $firewallContext, serialize($token));
		$session->save();

		$cookie = new Cookie($session->getName(), $session->getId());
		$this->client->getCookieJar()->set($cookie);

		$crawler = $this->client->request('GET', 'feed');

		$order = $crawler->filter('[data-feed-type]')->each(function (Crawler $node, $i) {
			return intval(substr($node->attr('href'),1 ));
		});

		for ($i = 0; $i < count($order) - 1; $i++) {
			$this->assertLessThan($order[$i], $order[$i + 1], 'unexpected ordering');
		}
	}
}