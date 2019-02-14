<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/3/18
 * Time: 11:29 AM
 */

namespace PapaLocal\Data\DataMapper;


use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\Exception\UnhandledRequestException;

class CreditCardMapper extends DataMapper
{
    /**
     * @inheritDoc
     */
    protected function toEntity(string $className, array $data)
    {
//        if ($className === CreditCard::class) {
//            $creditCard = $this->entityFactory->create(CreditCard::class);
//
//        }
//
//        //request not handled by me
        throw new UnhandledRequestException(sprintf('%s cannot map %s to entity.', __CLASS__, $className));
    }

    /**
     * @inheritDoc
     */
    protected function toTable(Entity $entity)
    {
        if (! $entity instanceof CreditCard) {
            // this mapper cannot handle the request
            throw new UnhandledRequestException(sprintf('%s expects Param 1 to be an instance of %s', __METHOD__,
                CreditCard::class));
        }

        $row = array(
            'customerId' => $entity->getCustomerId(),
            'firstName' => $entity->getFirstName(),
            'lastName' => $entity->getLastName(),
            'accountNumber' => $entity->getCardNumber(),
            'expirationDate' => $entity->getExpirationDate(),
            'addressId' => $entity->getAddress()->getId()
        );

        return $row;
    }

}