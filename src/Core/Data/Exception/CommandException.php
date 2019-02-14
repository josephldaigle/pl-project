<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/28/18
 */


namespace PapaLocal\Core\Data\Exception;

use Throwable;


/**
 * Class CommandException.
 *
 * Used when an exception occurs in a data command handler.
 *
 * @package PapaLocal\Core\Data\Exception
 */
class CommandException extends \Exception
{
    public function __construct($message = "", CommandExceptionCode $code = null, Throwable $previous = null)
    {
        // set default code
        if (is_null($code)) {
            $code = CommandExceptionCode::UNSPECIFIED();
        }

        parent::__construct($message, $code->getValue(), $previous);
    }

    /**
     * Provide a string description of the exception code.
     *
     * @return string
     */
    public function getCodeDescription(): string
    {
        $codeValue = $this->getCode();
        $codeKey = CommandExceptionCode::search($codeValue);

        return $this->getCode()->getDescription();
    }
}