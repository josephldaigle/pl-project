<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/9/18
 * Time: 9:10 PM
 */


namespace PapaLocal\Core\Notification;


use PapaLocal\Data\Ewebify;


/**
 * Class EmailMessageBuilder
 *
 * @package PapaLocal\Core\Notification
 */
class EmailMessageBuilder
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $sender = Ewebify::ADMIN_EMAIL;

    /**
     * @var array
     */
    private $recipients = [];

    /**
     * @var array
     */
    private $ccList = [];

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @var bool restrict clients to using only message() or usingTwigTemplate()
     */
    private $bodyIsSet = false;

    /**
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * EmailMessageBuilder constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;

        $this->from();
        $this->contentType();
    }

    /**
     * Set who the email is sent from.
     *
     * @param string $emailAddress
     *
     * @return EmailMessageBuilder
     */
    public function from(string $emailAddress = Ewebify::ADMIN_EMAIL): EmailMessageBuilder
    {
        $this->sender = $emailAddress;

        return $this;
    }

    /**
     * Adds a recipient to the list.
     *
     * @param string $emailAddress
     *
     * @return EmailMessageBuilder
     */
    public function sendTo(string $emailAddress): EmailMessageBuilder
    {
        $this->recipients[] = $emailAddress;

        return $this;
    }

    /**
     * Add an email address to the cc line.
     *
     * @param string $emailAddress
     *
     * @return EmailMessageBuilder
     */
    public function copy(string $emailAddress): EmailMessageBuilder
    {
        $this->ccList[] = $emailAddress;

        return $this;
    }

    /**
     * Sets the subject of the message.
     *
     * @param string $subject
     *
     * @return EmailMessageBuilder
     */
    public function subject(string $subject): EmailMessageBuilder
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Sets the body of the email by generating a twig template, using the $templateArgs.
     *
     * Clients should use either this function or message() to set the email body content, but not both.
     *
     * @param string $templateName          relative to the configured \Twig_Environment template path
     * @param array  $templateArgs          array of arguments that will be available to the twig template
     *
     * @return EmailMessageBuilder
     * @throws \BadFunctionCallException    if the message body has already been set
     */
    public function usingTwigTemplate(string $templateName, array $templateArgs = array()): EmailMessageBuilder
    {
        // check if body is already set
        if ($this->bodyIsSet) {
            throw new \BadFunctionCallException('Cannot call a function that sets the message body more than once per build.');
        }

        // generate template as string
        $this->body = $this->twig->render(
            $templateName,
            $templateArgs
        );

        $this->bodyIsSet = true;
        return $this;
    }

    /**
     * @param string $message
     *
     * @return EmailMessageBuilder
     * @throws \BadFunctionCallException    if the message body has already been set
     */
    public function message(string $message): EmailMessageBuilder
    {
        // check if body is already set
        if ($this->bodyIsSet) {
            throw new \BadFunctionCallException('Cannot call a function that sets the message body more than once per build.');
        }

        $this->body = $message;

        $this->bodyIsSet = true;
        return $this;
    }

    /**
     * Set the email content type.
     *
     * @param string $contentType
     *
     * @return EmailMessageBuilder
     */
    public function contentType(string $contentType = 'text/html'): EmailMessageBuilder
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Build an instance of email message.
     *
     * @return EmailMessageInterface
     */
    public function build(): EmailMessageInterface
    {
        //
        $this->validateCanBuild();
        $message = new EmailMessage($this->sender, $this->recipients, $this->ccList, $this->subject, $this->body, $this->contentType);

        // clear out builder object
        $this->clearData();

        return $message;
    }

    /**
     * Clears the builders fields, so that subsequent uses do not pollute one another.
     */
    protected function clearData()
    {
        $this->sender = Ewebify::ADMIN_EMAIL;
        $this->recipients = [];
        $this->subject = null;
        $this->ccList = [];
        $this->body = '';
        $this->bodyIsSet = false;
        $this->contentType = 'text/html';
    }

    /**
     * Validate that all required fields have been set.
     *
     * @throws \LogicException
     */
    protected function validateCanBuild()
    {
        if (count($this->recipients) < 1) {
            throw new \LogicException('At least one recipient must be specified to build the email message.');
        }

        if (is_null($this->subject) || empty($this->subject)) {
            throw new \LogicException('A subject must be specified to build the email message.');
        }

        if (false == $this->bodyIsSet) {
            throw new \LogicException('A message body must be specified to build the email message.');
        }

        return;
    }
}