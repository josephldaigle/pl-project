<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/24/18
 * Time: 8:07 PM
 */


namespace PapaLocal\Entity\Collection;


use PapaLocal\Entity\FeedItemInterface;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\ValueObject\Form\FeedItem;


/**
 * Class FeedList
 *
 * @package PapaLocal\Entity\Collection
 *
 * Model a collection of Feed items.
 */
class FeedList extends Collection
{
	public const SORT_ASC = 'ASC';
	public const SORT_DESC = 'DESC';

	/**
	 * @var FeedItem
	 */
	private $selectedItem;

	/**
	 * @var NotificationList
	 */
	private $notificationList;

	/**
	 * @inheritdoc
	 * @throws \InvalidArgumentException
	 */
	public function add($object, $key = null)
    {
    	if (! $object instanceof FeedItemInterface) {
    		throw new \InvalidArgumentException(sprintf('%s expects param 1 to be instance of %s. %s given.',
			    __METHOD__, FeedItemInterface::class, gettype($object)));
	    }

	    parent::add($object,$key);
    }

	/**
	 * Set's the currently chosen item. This is not an iterator cursor.
	 *
	 * @param FeedItem $selected
	 */
	public function setSelectedItem(FeedItem $selected)
	{
		$this->selectedItem = $selected;
	}

	/**
	 * @return mixed
	 */
	public function getSelectedItem()
	{
		return $this->selectedItem;
	}

	/**
	 * @return NotificationList
	 */
	public function getNotifications(): NotificationList
	{
		return $this->notificationList;
	}

	/**
	 * @param NotificationList $notificationList
	 */
	public function setNotifications(NotificationList $notificationList)
	{
		$this->notificationList = $notificationList;
	}

	/**
	 * Searches the feed by $type for item with $property = $value.
	 *
	 * @param string $type
	 * @param string $property
	 * @param string $value
	 *
	 * @return mixed|null
	 * @throws \InvalidArgumentException
	 */
	public function findTypeBy(string $type, string $property, string $value)
	{
		$item = null;

		switch ($type) {
			case 'notification':
				$item = $this->notificationList->findBy($property, $value);
				break;
			case 'referral':
				//TODO: Implement
				break;
			case 'agreement':
				//TODO: Implement
				break;
			default:
				// @see FeedItemInterface for valid types (FEED_TYPE_*)
				throw new \InvalidArgumentException(sprintf('%s expects param 1 to be a valid feed type. %s given.', __METHOD__, $property));
		}

		// return results of search
		return $item;
	}

	/**
	 * Sort the underlying feed items by the timeCreated property.
	 */
	public function sortByTimeCreated(string $order = self::SORT_ASC)
	{
		if (count($this->items) < 1) {
			throw new \BadFunctionCallException(sprintf('Unable to call %s on empty list', __METHOD__));
		}

		$items = $this->items;

		if ($order === self::SORT_DESC) {
			// sort descending
			usort( $items, function( $a, $b ) {
				if ($a->getTimeCreated() == $b->getTimeCreated()) {
					return ($a->getId() > $b->getId() ) ? -1 : 1;
				}

				return ( $a->getTimeCreated() > $b->getTimeCreated() ) ? - 1 : 1;
			} );
		} else {
			// sort ascending
			usort($items, function($a, $b) {
				if($a->getTimeCreated() == $b->getTimeCreated()){ return 0 ; }
				return ($a->getTimeCreated() < $b->getTimeCreated()) ? -1 : 1;
			});
		}

		$this->items = $items;
		return $this;
	}
}