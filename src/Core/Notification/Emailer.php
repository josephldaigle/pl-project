<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/9/18
 */


namespace PapaLocal\Core\Notification;


use Psr\Log\LoggerInterface;


/**
 * Class Emailer.
 *
 * @package PapaLocal\Core\Notification
 */
class Emailer implements EmailerInterface
{
    /**
     * @var EmailMessageBuilder
     */
    private $emailBuilder;

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Emailer constructor.
     *
     * @param EmailMessageBuilder $emailBuilder
     * @param \Swift_Mailer       $swiftMailer
     * @param LoggerInterface     $logger
     */
    public function __construct(
        EmailMessageBuilder $emailBuilder,
        \Swift_Mailer $swiftMailer,
        LoggerInterface $logger
    )
    {
        $this->emailBuilder = $emailBuilder;
        $this->swiftMailer  = $swiftMailer;
        $this->logger       = $logger;
    }


    /**
     * @param EmailMessageInterface $message
     * @param array                 $failedRecipients
     *
     * @return int
     */
    public function send(EmailMessageInterface $message, array &$failedRecipients = array()) : int
    {
        // create swift message
        $swiftMessage = (new \Swift_Message($message->getSubject()))
            ->setFrom($message->getSender())
            ->setBody($message->getBody())
            ->setContentType($message->getContentType());

        // send each message individually
        $successes = 0;
        foreach($message->getRecipients() as $address) {
            $swiftMessage->setTo($address);

            // send email
            $sent = $this->swiftMailer->send($swiftMessage);

            if (!$sent) {
                $failedRecipients[] = $address;
                $this->logger->debug(sprintf('Failed sending email to %s with subject: %s.', $address, $message->getSubject()), array($message));
            } else {
                $successes++;
            }

        }

        return $successes;
    }

    /**
     * @return EmailMessageBuilder
     */
    public function getMessageBuilder(): EmailMessageBuilder
    {
        return $this->emailBuilder;
    }
}