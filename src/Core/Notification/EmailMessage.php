<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/9/18
 * Time: 9:05 PM
 */


namespace PapaLocal\Core\Notification;


/**
 * Class EmailMessage
 *
 * @package PapaLocal\Core\Notification
 */
class EmailMessage implements EmailMessageInterface
{
    /**
     * @var string
     */
    private $sender;

    /**
     * @var array
     */
    private $recipients;

    /**
     * @var array
     */
    private $ccList;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $timeSent;

    /**
     * EmailMessage constructor.
     *
     * @param string $sender
     * @param array  $recipients
     * @param array  $ccList
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $timeSent
     */
    public function __construct(string $sender, array $recipients, array $ccList, string $subject, string $body, string $contentType, $timeSent = '')
    {
        $this->sender      = $sender;
        $this->recipients  = $recipients;
        $this->ccList      = $ccList;
        $this->subject     = $subject;
        $this->body        = $body;
        $this->contentType = $contentType;
        $this->timeSent    = $timeSent;
    }

    /**
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return array
     */
    public function getCcList(): array
    {
        return $this->ccList;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getTimeSent(): string
    {
        return $this->timeSent;
    }
}