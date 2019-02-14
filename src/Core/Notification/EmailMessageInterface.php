<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/9/18
 * Time: 9:08 PM
 */

namespace PapaLocal\Core\Notification;


/**
 * Class EmailMessageInterface
 *
 * Describe an email message.
 *
 * @package PapaLocal\Core\Notification
 */
interface EmailMessageInterface
{
    /**
     * @return string
     */
    public function getSender(): string;

    /**
     * @return array
     */
    public function getRecipients(): array;

    /**
     * @return array
     */
    public function getCcList(): array;

    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return string
     */
    public function getTimeSent(): string;
}