<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/27/18
 * Time: 9:31 PM
 */

namespace PapaLocal\Entity\Comparator;


use PapaLocal\Entity\Entity;


/**
 * Interface EntityComparatorInterface
 *
 * @package PapaLocal\Entity\Comparator
 *
 * Describe an entity comparator.
 */
interface EntityComparatorInterface
{
	/**
	 * Compares two entities.
	 *
	 * @param Entity $entity
	 *
	 * @return mixed
	 */
	public function compare(Entity $entity): bool;
}