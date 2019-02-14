<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/18/18
 * Time: 4:09 PM
 */


namespace PapaLocal\ReferralAgreement\Event;


use PapaLocal\Controller\Api\Billing\TransactionController;
use PapaLocal\Controller\Api\ReferralAgreementController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class RequestSubscriber
 *
 * Subscriber to the "kernel.request" event for referral agreements.
 * Alters controller actions used in different contexts when creating and publishing
 * referral agreements.
 *
 * This helps keep the logic inside of controllers to a minimum,
 * and allows controllers to respond correctly, based on request context.
 *
 * @package PapaLocal\ReferralAgreement\Event
 */
class RequestSubscriber implements EventSubscriberInterface
{
	/**
	 * Changes controller handling when a user adds an invitee to an agreement from the feed.
     *
	 * @param GetResponseEvent $event
	 */
	public function addInvitee(GetResponseEvent $event)
	{
		if ($event->getRequest()->request->has('context')
			&& strcasecmp($event->getRequest()->request->get('context'), 'addInviteeFromFeed') === 0) {

			// set the controller to addInviteeFromFeed
			$event->getRequest()->attributes->set('_controller', ReferralAgreementController::class . '::addInviteeFromFeed');
		}
	}

    /**
     * Handle when a user is creating an agreement, and adding funds using the modal forms.
     *
     * @param GetResponseEvent $event
     */
	public function addFundsToCreate(GetResponseEvent $event)
    {
        if ($event->getRequest()->request->has('context')
            && strcasecmp($event->getRequest()->request->get('context'), 'addFundsToCreateAgreement') === 0) {

            // set the controller
            $event->getRequest()->attributes->set('_controller', TransactionController::class . '::addFundsToCreateAgreement');
        }
    }

    /**
     * Handle when a user is adding funds from the feed to publish an agreement.
     *
     * @param GetResponseEvent $event
     */
    public function addFundsToPublish(GetResponseEvent $event)
    {
        if ($event->getRequest()->request->has('context')
            && strcasecmp($event->getRequest()->request->get('context'), 'addFundsFromFeedForReferralAgreement') === 0) {

            // set the controller
            $event->getRequest()->attributes->set('_controller', TransactionController::class . '::addFundsFromFeedForReferralAgreement');
        }
	}

    /**
     * {@inheritdoc}
     *
     * @return array
     */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => array(
			    array('addInvitee'),
                array('addFundsToCreate'),
                array('addFundsToPublish')
            )
		];
	}

}