<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/9/18
 */


namespace PapaLocal\Core\Notification;


/**
 * Class EmailerInterface.
 *
 * @package PapaLocal\Core\Notification
 */
interface EmailerInterface
{
    /**
     * @param EmailMessageInterface $emailMessage
     * @param array                 $failedRecipients
     *
     * @return int
     */
    public function send(EmailMessageInterface $emailMessage, array &$failedRecipients = array()): int;

    /**
     * Fetch a builder for creating email messages.
     *
     * @return EmailMessageBuilder
     */
    public function getMessageBuilder(): EmailMessageBuilder;
}