<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/9/17
 * Time: 10:43 PM
 */

namespace PapaLocal\Entity;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LogStatement.
 *
 * Model a log output statement.
 */
class LogStatement
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    const LOG = 'log';

    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank(
     *     message = "Level must be present.",
     *     groups = {"create"}
     * )
     */
    private $level;

    /**
     * @var string $message
     * @Type("string")
     * @Assert\NotBlank(
     *     message = "Message must contain a value.",
     *     groups = {"create"}
     * )
     */
    private $message;

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     *
     * @return LogStatement
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     *
     * @return LogStatement
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

}