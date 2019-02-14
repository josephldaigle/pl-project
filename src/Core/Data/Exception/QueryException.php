<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/2/17
 * Time: 4:24 PM
 */


namespace PapaLocal\Core\Data\Exception;

use Throwable;


/**
 * Class QueryException
 *
 * Used when an exception occurs in a query handler.
 *
 * @package PapaLocal\Core\Data\Exception
 */
class QueryException extends \Exception
{

    /**
     * QueryException constructor.
     *
     * @param string                  $message
     * @param QueryExceptionCode|null $code
     * @param Throwable|null          $previous
     */
    public function __construct($message = "", QueryExceptionCode $code = null, Throwable $previous = null)
    {
        // set default code
        if (is_null($code)) {
            $code = QueryExceptionCode::UNSPECIFIED();
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
        $codeKey = QueryExceptionCode::search($codeValue);

        return $this->getCode()->getDescription();
    }
}