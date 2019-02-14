<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/24/18
 */


namespace PapaLocal\Core\Messenger\Exception;


use Throwable;


/**
 * Class MessageNotAllowedException
 *
 * Thrown when a message is dispatched on the wrong bus.
 *
 * @package PapaLocal\Core\Messenger\Exception
 */
class MessageNotAllowedException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . 'Did you intend to send the message to this bus? Is the message configured correctly?', $code, $previous);
    }

}