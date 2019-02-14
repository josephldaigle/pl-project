<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/6/18
 */

namespace PapaLocal\Entity;

/**
 * Class ClassFactory.
 *
 * @package PapaLocal\Entity
 */
class ClassFactory
{
    public static function create(string $class)
    {
        return new $class();
    }

	public function getInstance(string $class)
	{
		return new $class();
    }
}