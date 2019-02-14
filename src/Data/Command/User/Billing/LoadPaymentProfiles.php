<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/3/18
 * Time: 10:48 PM
 */

namespace PapaLocal\Data\Command\User\Billing;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Collection\Collection;

/**
 * LoadPaymentProfiles.
 *
 * Loads a user's payment profiles into a given Collection.
 *
 * @package PapaLocal\Data\Command\User\Billing
 */
class LoadPaymentProfiles extends QueryCommand
{
    /**
     * @var int $userId
     */
    private $userId;

    /**
     * @var Collection
     */
    private $profileList;

    /**
     * LoadPaymentProfiles constructor.
     *
     * @param int        $userId
     * @param Collection $profileList a profile list to load the accounts into. The list may already values.
     */
    public function __construct(int $userId, Collection $profileList)
    {
        $this->userId = $userId;
        $this->profileList = $profileList;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            // query for credit cards
            $this->tableGateway->setTable('v_user_payment_profile');
            $creditCards = $this->tableGateway->findBy('userId', $this->userId);

            //build the profile list
            foreach($creditCards as $row) {

                // create credit card obj
                $creditCard = $this->serializer->denormalize(array(
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'customerId' => $row['customerId'],
                    'cardNumber' => $row['accountNumber'],
                    'expirationMonth' => substr($row['expirationDate'], 0, 2),
                    'expirationYear' => substr($row['expirationDate'], 2),
                    'cardType' => $row['type'],
                    'address' => $this->serializer->denormalize($row, Address::class, 'array')
                ), CreditCard::class, 'array');

                $this->profileList->add($creditCard);
            }

            return $this->profileList;

        } catch (\Exception $e) {
            throw $this->filterException($e);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}