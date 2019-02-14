<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/7/18
 * Time: 5:40 PM
 */


namespace PapaLocal\Core\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * CompanyViewFilterSubscriber.
 *
 * @package PapaLocal\Core\Event
 */
class CompanyViewFilterSubscriber implements EventSubscriberInterface
{

    public function setCompanyPerspective(FilterControllerEvent $event)
    {
        // exclude sub-requests
        if (! $event->isMasterRequest()) {
            return;
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'setCompanyPerspective'
        );
    }

}