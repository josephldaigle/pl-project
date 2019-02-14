<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/9/18
 */

namespace PapaLocal\Entity\Billing;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class PaymentProfile.
 *
 * @package PapaLocal\Entity\Billing
 */
class PaymentProfile extends Collection
{
    /**
     * Fetch the primary payment profile.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDefaultPayProfile()
    {
        foreach ($this->items as $item) {
            if ($item->isDefaultPayMethod()) {
                return $item;
            }
        }

        throw new \Exception('Unable to find a default payment profile.');
    }

    /**
     * Fetch the secondary payment profiles (non-primary).
     *
     * @return array
     */
    public function getAllSecondaryPayProfiles()
    {
        $profiles = $this->items;

        for ($i = 0; $i < count($profiles); $i++) {
            if ($profiles[$i]->isDefaultPayMethod()) {
                unset($profiles[$i]);
            }
        }

        return $profiles;
    }

    //TODO: Implement remainder of function
//	/**
//	 * Determine whether or not a payment account is in this list.
//	 *
//	 * @param PaymentAccountInterface $account
//	 */
//	public function contains(PaymentAccountInterface $account): bool
//	{
//		foreach ($this->items as $item) {
//
//		}
//    }

}