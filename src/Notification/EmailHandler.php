<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/12/18
 * Time: 2:28 PM
 */

namespace PapaLocal\Notification;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Notification\ValueObject\Strategy\Strategy;
use Psr\Log\LoggerInterface;


/**
 * Class EmailHandler
 *
 * @package PapaLocal\Notification
 */
class EmailHandler
{
    /**
     * @var Emailer
     */
    private $emailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EmailHandler constructor.
     *
     * @param Emailer         $emailer
     * @param LoggerInterface $logger
     */
    public function __construct(Emailer $emailer,
                                LoggerInterface $logger)
    {
        $this->emailer = $emailer;
        $this->logger  = $logger;
    }

    /**
     * @param $notification
     *
     * @return int
     * @throws \Exception
     */
    function __invoke(AbstractNotification $notification)
    {
        if (! $notification instanceof EmailStrategyInterface
            || ! in_array(Strategy::EMAIL()->getValue(), $notification->getStrategies())) {
            return;
        }

        try {
            // send email
            $emailMessage = $this->emailer->getMessageBuilder()
                  ->sendTo($notification->getRecipient())
                  ->subject($notification->getSubject())
                  ->usingTwigTemplate($notification->getTemplateName(), $notification->getTemplateArgs())
                  ->build();

            $this->emailer->send($emailMessage);

            return;

        } catch (\Exception $exception) {

            $this->logger->error('An error occurred while sending an email.', array(
                'exception' => $exception,
                'notification' => $notification,
                'email' => (isset($email)) ? $email : null
            ));

            throw $exception;
        }
    }

}