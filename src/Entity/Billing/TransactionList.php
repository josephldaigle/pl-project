<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/16/18
 */

namespace PapaLocal\Entity\Billing;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class TransactionList.
 *
 * @package PapaLocal\Entity\Billing
 *
 * Model a collection of transactions for a user.
 */
class TransactionList extends Collection
{
    /**
     * For specifying sort ordering
     */
    const SORT_ORDER_ASC = 'ASC';
    const SORT_ORDER_DESC = 'DESC';

    /**
     * Fetch all transactions within date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return TransactionList
     */
    public function findByDate(string $startDate, string $endDate)
    {

        $startDate = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

        if ($startDate >= $endDate) {
            throw new \InvalidArgumentException('End date cannot be before start date.');
        }

        $temp = clone $this;

        foreach(array_keys($temp->all()) as $key) {
            $txnTimeCreated = date('Y-m-d H:i:s', strtotime($temp->get($key)->getTimeCreated()));

            if (! ($txnTimeCreated >= $startDate && $txnTimeCreated <= $endDate)) {

                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Fetch all transactions for a user.
     *
     * @param int $userId
     * @return TransactionList
     */
    public function findByUserId(int $userId)
    {
        $temp = clone $this;

        foreach(array_keys($temp->all()) as $key) {

            if (! ($temp->get($key)->getUserId() === $userId)) {
                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Fetch all transactions with a specified dollar amount (credit AND debit)
     *
     * @param float $amount
     * @return TransactionList
     */
    public function findByAmount(float $amount)
    {
        $temp = clone $this;

        foreach(array_keys($temp->all()) as $key) {

            if (! ($temp->get($key)->getAmount() === $amount)) {
                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Fetch all deposits.
     *
     * A deposit is an instance of the user's card being charged, and his PapaLocal balance being increased.
     *
     * @return TransactionList
     */
    public function getAllDeposits()
    {
        $temp = $this;

        foreach(array_keys($temp->all()) as $key) {

            if (! ($temp->get($key)->getType() === 'credit')) {
                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Fetch all withdrawals.
     *
     * A withdrawal occurs when a user removes money from the system.
     *
     * @return TransactionList
     */
    public function getAllWithdrawals()
    {
        $temp = clone $this;

        foreach(array_keys($temp->all()) as $key) {

            if (! ($temp->get($key)->getDescription() === 'Cash Withdrawal')) {
                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Fetch all charges.
     *
     * @return TransactionList
     */
    public function getAllCharges()
    {
        $temp = clone $this;

        foreach(array_keys($temp->all()) as $key) {

            if (! ($temp->get($key)->getType() === 'debit' && $temp->get($key)->getDescription() !== 'Cash Withdrawal')) {
                $temp->remove($key);
            }
        }

        return $temp;
    }

    /**
     * Sort the lists elements by the 'timeCreated' timestamp.
     *
     * @param string $order
     * @return $this
     */
    public function sortByDate(string $order = self::SORT_ORDER_ASC)
    {
        if (count($this->items) < 1) {
            throw new \BadFunctionCallException(sprintf('Unable to call %s on empty list', __METHOD__));
        }

        $items = $this->items;

        if ($order === self::SORT_ORDER_DESC) {
            // sort descending
            usort($items, function ($a, $b) {
                if ($a->getTimeCreated() == $b->getTimeCreated()) {
                    return ($a->getId() > $b->getId()) ? -1 : 1;
                }

                return ($a->getTimeCreated() > $b->getTimeCreated()) ? -1 : 1;
            });
        } else {
            // sort ascending
            usort($items, function ($a, $b) {
                if ($a->getTimeCreated() == $b->getTimeCreated()) {
                    return 0;
                }
                return ($a->getTimeCreated() < $b->getTimeCreated()) ? -1 : 1;
            });
        }

        $this->items = $items;
        return $this;
    }
}