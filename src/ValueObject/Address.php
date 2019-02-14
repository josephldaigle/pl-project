<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/18/18
 * Time: 8:24 PM
 */


namespace PapaLocal\ValueObject;


/**
 * Class Address
 *
 * @package PapaLocal\ValueObject
 */
class Address
{
	/**
	 * @var string
	 */
	private $streetAddress;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $state;

	/**
	 * @var string
	 */
	private $postalCode;

	/**
	 * @return mixed
	 */
	public function getStreetAddress()
	{
		return $this->streetAddress;
	}

	/**
	 * @param mixed $streetAddress
	 *
	 * @return Address
	 */
	public function setStreetAddress($streetAddress): Address
	{
		$this->streetAddress = $streetAddress;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param mixed $city
	 *
	 * @return Address
	 */
	public function setCity($city): Address
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param mixed $state
	 *
	 * @return Address
	 */
	public function setState($state): Address
	{
		$this->state = $state;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
	}

	/**
	 * @param string $postalCode
	 *
	 * @return Address
	 */
	public function setPostalCode(string $postalCode): Address
	{
		$this->postalCode = $postalCode;

		return $this;
	}
}