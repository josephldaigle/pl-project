<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/7/18
 * Time: 5:38 PM
 */

namespace PapaLocal\Entity\Notification;


use PapaLocal\Entity\Collection\Collection;


/**
 * NotificationList.
 *
 * @package PapaLocal\Entity\Notification
 */
class NotificationList extends Collection
{
	public const SORT_ASC = 'ASC';
	public const SORT_DESC = 'DESC';

	/**
	 * Sort the lists elements by the 'sent' timestamp.
	 *
	 * @param string $order
	 *
	 * @return array
	 */
	public function sortByTimeSent( string $order = null )
	{
		if (count($this->items) < 1) {
			throw new \BadFunctionCallException(sprintf('Unable to call %s on empty list', __METHOD__));
		}

		$items = $this->items;

		if ($order === self::SORT_DESC) {
			// sort descending
			usort( $items, function( $a, $b ) {
				if ($a->getTimeSent() == $b->getTimeSent()) {
//					return ( $a->getId() > $b->getId() ) ? - 1 : 1;
                    return 0;
				}

				return ( $a->getTimeSent() > $b->getTimeSent() ) ? - 1 : 1;
			} );
		} else {
			// sort ascending
			usort($items, function($a, $b) {
				if($a->getTimeSent() == $b->getTimeSent()){ return 0 ; }
				return ($a->getTimeSent() < $b->getTimeSent()) ? -1 : 1;
			});
		}

		$this->items = $items;
		return $this->items;
	}

    /**
     * Fetch a subset of the list's elements by their indexes.
     *
     * @param int $start
     * @param int $end
     *
     * @return NotificationList
     * @throws \InvalidArgumentException
     */
	public function sliceByIndexRange( int $start, int $end )
	{
	    if($end < $start) {
	        throw new \InvalidArgumentException(sprintf('Param 2 provided to %s must be greater than param 1.', __METHOD__));
        }

	    $items = clone $this;
		foreach ($items->all() as $key => $val) {

		    if(! is_numeric($key)){
                throw new \InvalidArgumentException(sprintf('Unable to call %s when index is a non integer value', __METHOD__));
            }

			if ($key < $start || $key > $end) {
				$items->remove($key);
			}
		}

		return $items;
	}

    /**
     * Get the number of unread messages in the list.
     *
     * @return int
     */
    public function countUnreadMessages(): int
    {
        $filtered = array_filter($this->items, function($a){
            return ($a->isRead() === false);
        });

        return count($filtered);
    }
}