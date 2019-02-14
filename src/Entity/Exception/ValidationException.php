<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/28/18
 * Time: 6:38 PM
 */

namespace PapaLocal\Entity\Exception;


use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;


/**
 * Class ValidationException
 *
 * @package PapaLocal\Entity\Exception
 *
 * Thrown when an validation error occurs.
 */
class ValidationException extends \Exception
{
	/**
	 * @var ConstraintViolationListInterface
	 */
	private $validationErrors;

	public function __construct($message = "",
	                            $code = 0,
	                            Throwable $previous = null,
	                            ConstraintViolationListInterface $validationErrors = null)
	{
		parent::__construct($message, $code, $previous);
		$this->validationErrors = $validationErrors;
	}

	/**
	 * @return ConstraintViolationListInterface
	 */
	public function getValidationErrors(): ConstraintViolationListInterface
	{
		return $this->validationErrors;
	}
}