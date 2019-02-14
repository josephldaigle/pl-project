<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 10:16 PM
 */

namespace PapaLocal\Core\Data\Exception;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class QueryExceptionCode
 *
 * Exception codes for data queries.
 *
 * @package PapaLocal\Core\Data\Exception
 */
class QueryExceptionCode extends AbstractEnum
{
    private const UNSPECIFIED = 0;          // default code
    private const NOT_FOUND = 100;   // the record cannot be found

    /**
     * @var array   a mapping of messages describing each exception code.
     */
    private $descriptions = [
        'UNSPECIFIED' => 'No code was provided when the exception was generated.',
        'NOT_FOUND' => 'The query did not produce any results.'
    ];

    /**
     * @return string
     * @throws \LogicException
     */
    public function getCodeDescription(): string
    {
        if (! in_array($this->getKey(), array_keys($this->descriptions))) {
            throw new \LogicException(sprintf('The code %s does not have a description defined in %s.', $this->getValue(), __CLASS__));
        }

        return $this->descriptions[$this->getKey()];
    }
}