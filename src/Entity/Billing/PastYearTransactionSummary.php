<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/26/18
 * Time: 10:12 AM
 */

namespace PapaLocal\Entity\Billing;


use PapaLocal\Entity\Collection\Collection;

/**
 * PastYearTransactionSummary.
 *
 * @package PapaLocal\Entity\Billing
 */
class PastYearTransactionSummary extends Collection
{
	/**
	 * @return PastYearTransactionSummary
	 */
	public function sortByDate(): PastYearTransactionSummary
	{
		$sorted = array();
		foreach ($this->items as $summary) {
			$sorted[$summary->getDate()] = $summary;
		}

		$this->items = $sorted;
		krsort($this->items);

		return $this;
	}
}