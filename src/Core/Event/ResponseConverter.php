<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 8/7/18
 * Time: 3:37 PM
 */

namespace PapaLocal\Core\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class ResponseConverter
 *
 * @package PapaLocal\Core\Event
 */
class ResponseConverter implements EventSubscriberInterface
{

    /**
     * Return only Json formatted response, when the request content-type is Json
     *
     * @param FilterResponseEvent $event
     */
    public function convertToJson(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if($response instanceof JsonResponse){
            return;
        }

        $request = $event->getRequest();

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'))->all();

        if(count($acceptHeader) === 1 && array_key_exists('application/json', $acceptHeader)) {
            $response->headers->set('Content-Type', 'application/json');
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'convertToJson'
        );
    }

}