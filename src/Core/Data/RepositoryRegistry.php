<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/25/18
 * Time: 9:46 PM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class RepositoryRegistry
 *
 * A registry for Repository(s).
 *
 * @package PapaLocal\Core\Data
 */
class RepositoryRegistry extends Collection
{
	/**
	 * @inheritdoc
	 */
	public function add($repository, $key = null)
	{
		if (! $repository instanceof AbstractRepository){
			throw new \InvalidArgumentException(sprintf('%s expects param 1 to be an instance of %s. %s given.', __METHOD__, AbstractRepository::class, get_class($repository)));
		}

		parent::add($repository, $key);
	}
}