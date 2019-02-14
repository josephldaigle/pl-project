<?php
/**
 * Created by Joseph Daigle.
 * Date: 8/21/18
 * Time: 7:30 PM
 */


namespace PapaLocal\Test;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\DependencyInjection\Container;


/**
 * WebTestCase.
 *
 * Decorator class for Syfmony WebTestCase.
 *
 * Extend this class in functional tests to use
 * the container without having to boot the kernel
 * or fetch the container.
 *
 * Implementing classes should override the setUp() function and use it to
 * set the tables that should be loaded for each test. This could also be
 * done within each test function if desired.
 *
 * @package PapaLocal\Test
 */
abstract class WebTestCase extends SymfonyWebTestCase
{
	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var Container
	 */
	protected $diContainer;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp()
	{

		// set up fixtures
		$this->client = self::createClient(array(), array(
			'PHP_AUTH_USER' => 'test@ewebify.com',
			'PHP_AUTH_PW'   => 'testUser123!!',
		));
		$this->client->followRedirects();

		parent::setUp();

		// fetch a container that allows access to private services
		$this->diContainer = self::$container;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function tearDown()
	{
		parent::tearDown();

		$this->client = null;
		$this->diContainer = null;
	}
}