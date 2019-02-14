<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/1/18
 * Time: 5:43 AM
 */


namespace PapaLocal\Billing\Event;


use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Notification\NotificationFactory;
use PapaLocal\Notification\Notifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class DepositCompletedSubscriber
 *
 * Event subscriber for DepositCompletedEvent.
 *
 * @package PapaLocal\Billing\Event
 */
class DepositCompletedSubscriber implements EventSubscriberInterface
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * DepositCompletedSubscriber constructor.
     *
     * @param Notifier            $notifier
     * @param NotificationFactory $notificationFactory
     */
    public function __construct(Notifier $notifier, NotificationFactory $notificationFactory)
    {
        $this->notifier = $notifier;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * Send out a notification to the user who has deposited funds.
     *
     * @param DepositCompletedEvent $event
     *
     * @throws \PapaLocal\Entity\Exception\NotificationException
     */
    public function sendNotification(DepositCompletedEvent $event)
    {

        $paymentAccount = $event->getPaymentAccount();
        if ($paymentAccount instanceof CreditCardInterface) {
            $templateArgs = array(
                'accountNumber'  => $paymentAccount->getCardNumber(),
                'cardholder'     => $paymentAccount->getFirstName() . ' ' . $paymentAccount->getLastName(),
                'expirationDate' => $paymentAccount->getExpirationDate(),
                'depositAmount'  => $event->getDepositAmount(),
                'accountBalance' => $event->getAccountBalance()
            );

            $notification = $this->notificationFactory->createManualDepositSuccess($event->getDepositAmount(), $event->getAccountBalance(), $event->getRecipient()->getUsername(), $templateArgs);

            $this->notifier->sendUserNotification($event->getRecipient()->getGuid(), $notification);
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
            DepositCompletedEvent::class => 'sendNotification'
        ];
    }


}