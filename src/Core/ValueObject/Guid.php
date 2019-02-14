<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/7/18
 * Time: 9:37 PM
 */


namespace PapaLocal\Core\ValueObject;


/**
 * Class Guid
 *
 * @package PapaLocal\ValueObject
 */
class Guid implements GuidInterface
{
	/**
	 * @var string
	 */
	private $value;

	/**
	 * Guid constructor.
	 *
	 * @param string $value
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function value(): string
	{
		return $this->value;
	}
}