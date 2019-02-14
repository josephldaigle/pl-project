<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/27/18
 * Time: 11:07 AM
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class StatusHistory
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class StatusHistory
{
    /**
     * @var Collection
     */
    private $statusHistory;

    /**
     * StatusHistory constructor.
     *
     * @param Collection $statusHistory
     */
    public function __construct(Collection $statusHistory)
    {
       $this->statusHistory = $statusHistory;
    }

    /**
     * @param AgreementStatus $status
     *
     * @throws \InvalidArgumentException
     */
    public function add(AgreementStatus $status)
    {
        $this->statusHistory->prepend($status);
    }

    /**
     * @param Collection $statusHistory
     *
     * @throws \InvalidArgumentException
     */
    protected function addAll(Collection $statusHistory)
    {
        foreach ($statusHistory as $key => $status) {
            if (! $status instanceof AgreementStatus) {
                throw new \InvalidArgumentException(sprintf('Param 1 provided to %s expects all elements used to be instances of %s. Element at index %s is not.', __METHOD__, AgreementStatus::class, $key));
            }
        }

        $this->statusHistory = $statusHistory;
    }

    /**
     * @return Collection
     */
    public function getHistory(): Collection
    {
        return $this->statusHistory;
    }

    /**
     * Retrieve the most recent status entry.
     *
     * @throws \LogicException if the underlying history is empty. use count() to check beforehand.
     */
    public function getCurrentStatus()
    {
        if ($this->statusHistory->count() < 1) {
            // history is empty, therefore status is unknown
            return 'Unknown';
        }

        // prevent unexpected error if somehow the underlying collection is comprised of unexpected objects (missing timeCreated)
        $first = $this->statusHistory->first();
        $all = $this->statusHistory->all();

        // handle objects
        if (is_object($first) && method_exists($first, 'getTimeUpdated')) {
            $mostRecent = array_reduce($all, function($carry, $item) {
                if (strtotime($carry->getTimeUpdated()) > strtotime($item->getTimeUpdated())) {
                    return $carry;
                } else {
                    return $item;
                }
            }, $all[0]);

            return $mostRecent;
        }


        // handle arrays
        if (is_array($first) && array_key_exists('timeUpdated', $first)) {
            $mostRecent = array_reduce($all, function($carry, $item) {
                if (strtotime($carry['timeUpdated']) > strtotime($item['timeUpdated'])) {
                    return $carry;
                } else {
                    return $item;
                }
            }, $all[0]);

            return $mostRecent;
        }

        throw new \LogicException(sprintf('The underlying collection must contain objects or arrays which possess a timeUpdated attribute or key, in %s', __METHOD__));

    }
}