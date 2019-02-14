<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/20/18
 * Time: 12:50 PM
 */


namespace PapaLocal\Notification;


use PapaLocal\Core\Notification\EmailerInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Data\Ewebify;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Notification\Data\PersonNotificationRepository;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Entity\Exception\NotificationException;
use Psr\Log\LoggerInterface;


/**
 * Class Notifier
 *
 * A service class for sending notifications.
 *
 * @package PapaLocal\Notification
 */
class Notifier
{
	/**
	 * @var NotificationRepository
	 */
	private $notificationRepository;

    /**
     * @var PersonNotificationRepository
     */
	private $personNotificationRepository;

	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

    /**
     * @var EmailerInterface
     */
	private $emailer;

    /**
     * @var GuidGeneratorInterface
     */
	private $guidGenerator;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

    /**
     * Notifier constructor.
     *
     * @param NotificationRepository       $notificationRepository
     * @param PersonNotificationRepository $personNotificationRepository
     * @param \Swift_Mailer                $mailer
     * @param EmailerInterface             $emailer
     * @param GuidGeneratorInterface       $guidGenerator
     * @param \Twig_Environment            $twig
     * @param LoggerInterface              $logger
     */
    public function __construct(NotificationRepository $notificationRepository, PersonNotificationRepository $personNotificationRepository, \Swift_Mailer $mailer, EmailerInterface $emailer, GuidGeneratorInterface $guidGenerator, \Twig_Environment $twig, LoggerInterface $logger)
    {
        $this->notificationRepository = $notificationRepository;
        $this->personNotificationRepository = $personNotificationRepository;
        $this->mailer = $mailer;
        $this->emailer = $emailer;
        $this->guidGenerator = $guidGenerator;
        $this->twig = $twig;
        $this->logger = $logger;
    }


    /**
	 * Send a notification.
	 *
	 * @param GuidInterface         $userGuid
	 * @param NotificationInterface $notification
	 *
	 * @throws NotificationException
	 */
	public function sendUserNotification(GuidInterface $userGuid, NotificationInterface $notification)
	{
		try {

		    foreach ($notification->getStrategies() as $strategy) {

				switch ($strategy) {
					case AbstractNotification::STRATEGY_EMAIL;
						// send email

						if ($this->sendEmail($notification)) {
							$this->logger->info(sprintf('An email has been sent to %s with subject %s.', $userGuid->value(), $notification->getSubject()), array('notification' => $notification));
						}

						break;
					case AbstractNotification::STRATEGY_SMS;
						// send sms
						// $this->sendSMS($notification);
						break;
					case AbstractNotification::STRATEGY_APP;
						// save to db
                        $notification->setGuid($this->guidGenerator->generate());
                        $this->notificationRepository->save($userGuid, $notification);
						break;
					default:
						break;
				}
			}

		} catch (\Exception $exception) {
			throw new NotificationException(sprintf('Unable to send the notification as expected: %s.', $exception->getMessage()), $exception->getCode(), $exception);
		}
	}

    /**
     * @deprecated removed in 2.0
     * Send a notification to a non-user.
     *
     * @param int                   $personId
     * @param NotificationInterface $notification
     * @throws NotificationException
     */
	public function sendPersonNotification(int $personId, NotificationInterface $notification)
    {
        try {

            foreach ($notification->getStrategies() as $strategy) {

                switch ($strategy) {
                    case AbstractNotification::STRATEGY_EMAIL;
                        // send email

                        if ($this->sendEmail($notification)) {
                            $this->logger->info(sprintf('An email has been sent to person %s with subject %s.', $personId, $notification->getSubject()), array('notification' => $notification));
                        }

                        break;
                    case AbstractNotification::STRATEGY_SMS;
                        // send sms
                        // $this->sendSMS($notification);
                        break;
                    case AbstractNotification::STRATEGY_APP;
                        // save to db
                        $notification->setGuid($this->guidGenerator->generate());
                        $this->personNotificationRepository->saveNotification($personId, $notification);
                        break;
                    default:
                        break;
                }
            }

        } catch (\Exception $exception) {
            throw new NotificationException('Unable to send the notification as expected.', $exception->getCode(), $exception);
        }
    }

    /**
     * @param NotificationInterface $notification
     * @return bool
     * @throws \Exception
     */
	private function sendEmail(NotificationInterface $notification): bool
	{
		try {
			// send email
			if (! $notification instanceof EmailStrategyInterface) {
				$this->logger->critical(sprintf('To send a notification by email, it must implement %s.', EmailStrategyInterface::class, array('notification' => $notification)));
				return false;
			}

			$emailMessage = $this->emailer->getMessageBuilder()
                ->sendTo($notification->getRecipient())
                ->subject($notification->getSubject())
                ->usingTwigTemplate($notification->getTemplateName(), $notification->getTemplateArgs())
                ->build();

			return $this->emailer->send($emailMessage);

		} catch (\Exception $exception) {

			$this->logger->error('An error occurred while sending an email.', array(
				'exception' => $exception,
				'notification' => $notification,
				'email' => (isset($email)) ? $email : null
			));

			throw $exception;
		}

	}

    /**
     *
     * @param NotificationInterface $notification
     *
     * @return bool
     * @throws \LogicException
     */
	private function sendSMS(NotificationInterface $notification): bool
	{
		// TODO: Finish implementing
		throw new \LogicException(sprintf('The function %s not implemented.', __METHOD__));
	}

}