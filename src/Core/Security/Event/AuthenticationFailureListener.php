<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/29/19
 * Time: 12:47 PM
 */


namespace PapaLocal\Core\Security\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


/**
 * AuthenticationFailureListener.
 *
 * @package PapaLocal\Core\Security\Event
 */
class AuthenticationFailureListener implements EventSubscriberInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * AuthenticationFailureListener constructor.
     *
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @return RedirectResponse
     */
    public function authenticationFailed(AuthenticationFailureEvent $event)
    {

        $this->flashBag->add('danger', 'The username and password you used were not recognized.');
        return new RedirectResponse('/login', 302);

    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'security.authentication.failure' => 'authenticationFailed'
        ];
    }

}