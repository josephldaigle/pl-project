<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/26/18
 * Time: 10:30 AM
 */

namespace PapaLocal\Entity\Billing;


use PapaLocal\Entity\ComparableInterface;

/**
 * MonthlyTransactionSummary.
 *
 * @package PapaLocal\Entity\Billing
 */
class MonthlyTransactionSummary implements ComparableInterface
{
	/**
	 * @var int
	 */
	private $userId;

	/**
	 * @var string
	 */
	private $date;

	/**
	 * @var float
	 */
	private $begBalance;

	/**
	 * @var float
	 */
	private $monthTotal;

	/**
	 * @var float
	 */
	private $endBalance;

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 *
	 * @return MonthlyTransactionSummary
	 */
	public function setUserId(int $userId): MonthlyTransactionSummary
	{
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param string $date
	 *
	 * @return MonthlyTransactionSummary
	 */
	public function setDate(string $date): MonthlyTransactionSummary
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBegBalance()
	{
		return $this->begBalance;
	}

	/**
	 * @param float $begBalance
	 *
	 * @return MonthlyTransactionSummary
	 */
	public function setBegBalance(float $begBalance): MonthlyTransactionSummary
	{
		$this->begBalance = $begBalance;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMonthTotal()
	{
		return $this->monthTotal;
	}

	/**
	 * @param float $monthTotal
	 *
	 * @return MonthlyTransactionSummary
	 */
	public function setMonthTotal(float $monthTotal): MonthlyTransactionSummary
	{
		$this->monthTotal = $monthTotal;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEndBalance()
	{
		return $this->endBalance;
	}

	/**
	 * @param float $endBalance
	 *
	 * @return MonthlyTransactionSummary
	 */
	public function setEndBalance(float $endBalance): MonthlyTransactionSummary
	{
		$this->endBalance = $endBalance;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function compare($objectOne, $objectTwo): bool
	{

		if (!$objectOne instanceof MonthlyTransactionSummary
			|| !$objectTwo instanceof MonthlyTransactionSummary) {
			throw new \InvalidArgumentException(sprintf('Both arguments supplied to %s must be of type %s. 1) %s and 2) %s given', __METHOD__, MonthlyTransactionSummary::class, gettype($objectOne), gettype($objectTwo)));
		}

		return strcasecmp($objectOne->getDate(), $objectTwo->getDate());
	}


}