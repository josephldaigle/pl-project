<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/16/18
 * Time: 11:07 AM
 */


namespace PapaLocal\Core\Event;


use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\Data\Repository\Person\PersonRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class RequestDataProviderSubscriber
 *
 * @package PapaLocal\Core\Event
 */
class RequestDataProviderSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryRegistry
     */
	private $repositoryRegistry;

    /**
     * RequestDataProviderSubscriber constructor.
     *
     * @param RepositoryRegistry $repositoryRegistry
     */
	public function __construct(RepositoryRegistry $repositoryRegistry)
	{
		$this->repositoryRegistry = $repositoryRegistry;
	}

	/**
	 * Load a registry of Person objects into the request.
	 *
	 * @param GetResponseEvent $event
	 */
	public function loadPersonRegistry(GetResponseEvent $event)
	{
		$request = $event->getRequest();

		$request->attributes->add(array('personRegistry' => $this->repositoryRegistry->get(PersonRepository::class)->loadAll()));
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return array(
			KernelEvents::REQUEST => 'loadPersonRegistry',
		);
	}

}