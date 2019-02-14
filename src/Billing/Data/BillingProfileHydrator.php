<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/11/18
 */


namespace PapaLocal\Billing\Data;


use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Billing\PaymentProfile;
use PapaLocal\Entity\Entity;


/**
 * Class BillingProfileHydrator.
 *
 * @package PapaLocal\Billing\Data
 */
class BillingProfileHydrator extends AbstractHydrator
{
    /**
     * @inheritdoc
     */
    public function setEntity(Entity $entity)
    {
        if (! $entity instanceof BillingProfile || !is_numeric($entity->getUserId())) {
            throw new \InvalidArgumentException(sprintf('Cannot hydrate instance of %s with %s',
                get_class($entity), __CLASS__));
        }

        $this->entity = $entity;
    }

    /**
     * @inheritdoc
     */
    public function hydrate(): Entity
    {
        $this->entity->setPaymentProfile($this->fetchPaymentProfiles());

        $balanceData = $this->fetchAccountBalance();

        $this->entity->setBalance($balanceData['balance']);
        $this->entity->setAvailableBalance($balanceData['availableBalance']);

        return $this->entity;
    }

    /**
     * Hydrate a collection with the user's credit card profiles.
     *
     * @return PaymentProfile
     */
    private function fetchPaymentProfiles()
    {
        $collection = $this->serializer->denormalize(array(), PaymentProfile::class, 'array');

        // load the user's credit card profiles
        $this->tableGateway->setTable('v_user_payment_profile');
        $profileRows = $this->tableGateway->findBy('userId', $this->entity->getUserId());

        foreach ($profileRows as $row) {
            $address = $this->serializer->denormalize(array(
                'id' => $row['addressId'],
                'streetAddress' => $row['streetAddress'],
                'city' => $row['city'],
                'state' => $row['state'],
                'postalCode' => $row['postalCode'],
                'country' => $row['country']
            ), Address::class, 'array');

            $creditCard = $this->serializer->denormalize(array(
                'id' => $row['id'],
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'customerId' => $row['customerId'],
                'cardNumber' => $row['accountNumber'],
                'cardType' => $row['type'],
                'expirationMonth' => substr($row['expirationDate'], 0, 2),
                'expirationYear' => substr($row['expirationDate'], 2),
                'isDefaultPayMethod' => $row['defaultPayMethod']
            ), CreditCard::class, 'array');

            $creditCard->setAddress($address);
            $collection->add($creditCard);
        }

        return $collection;
    }

    /**
     * Retrieve the user's balance from storage.
     */
    protected function fetchAccountBalance()
    {
        $this->tableGateway->setTable('v_user_balance');
        $result = $this->tableGateway->findBy('userId', $this->entity->getUserId());

        $balance = [
            'balance' => 0.00,
            'availableBalance' => 0.00
        ];

        if (count($result) > 0) {
            $balance['balance'] = $result[0]['balance'] ? $result[0]['balance'] : 0.00;
            $balance['availableBalance'] = $result[0]['availableBalance'] ? $result[0]['availableBalance'] : 0.00;
        }

        return $balance;
    }
}