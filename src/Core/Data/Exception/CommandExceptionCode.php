<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/13/18
 * Time: 10:13 PM
 */

namespace PapaLocal\Core\Data\Exception;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class CommandExceptionCode
 *
 * @package PapaLocal\Core\Data\Exception
 */
class CommandExceptionCode extends AbstractEnum
{
    private const UNSPECIFIED = 0;          // default code
    private const NOT_FOUND = 100;
    private const UNIQUE_CONSTRAINT_VIOLATION = 200;
    private const FOREIGN_KEY_CONSTRAINT_VIOLATION = 300;

    /**
     * @var array   a mapping of messages describing each exception code.
     */
    private $descriptions = [
        'UNSPECIFIED' => 'No code was provided when the exception was generated.',
        'NOT_FOUND' => 'A query was executed that did not return expected results, which the command is dependent upon.',
        'UNIQUE_CONSTRAINT_VIOLATION' => 'A UNIQUE table constraint has prevented the operation.',
        'FOREIGN_KEY_CONSTRAINT_VIOLATION' => 'A FOREIGN_KEY table constraint has prevented the operation from succeeding.'
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