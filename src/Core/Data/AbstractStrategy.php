<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/15/18
 * Time: 3:40 PM
 */


namespace PapaLocal\Core\Data;


/**
 * Class AbstractStrategy
 *
 * @package PapaLocal\Core\Data
 */
abstract class AbstractStrategy
{
	/**
	 * @var DataResourcePool
	 */
	protected $dataResources;

	/**
	 * AbstractStrategy constructor.
	 *
	 * @param DataResourcePool $dataResourcePool
	 */
	public function __construct(DataResourcePool $dataResourcePool)
	{
		$this->dataResources = $dataResourcePool;
	}
}