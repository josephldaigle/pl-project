<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/27/17
 * Time: 8:49 PM
 */

namespace PapaLocal\Data\Command\User\Billing;

use PapaLocal\Data\Command\Address\AddressExists;
use PapaLocal\Data\Command\Address\CreateAddress;
use PapaLocal\Data\Command\Address\LoadAddress;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Entity\Exception\Data\CreditCardExistsException;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * CreateCreditCardProfile.
 *
 * Stores a user's credit card profile.
 *
 * Does not store PCI sensitive information.
 */
class CreateCreditCardProfile extends QueryCommand
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var CreditCardInterface
     */
    private $creditCard;

    /**
     * CreateCreditCardProfile constructor.
     *
     * @param int                 $userId
     * @param CreditCardInterface $creditCard
     */
    public function __construct(int $userId, CreditCardInterface $creditCard)
    {
        $this->userId = $userId;
        $this->creditCard = $creditCard;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        $this->tableGateway->connection->beginTransaction();

        try {
            $addressId = null;

            // check if address exists
            $addressExistsCmd = $this->commandFactory->createCommand(AddressExists::class,
                array($this->creditCard->getAddress()));
            $addressExists = $addressExistsCmd->execute($this->tableGateway, $this->mapper, $this->serializer,
                $this->commandFactory);

            if (! is_null($this->creditCard->getAddress()->getId())) {
                // address provided with card has an id
                $addressId = $this->creditCard->getAddress()->getId();

            } else if (false === $addressExists) {
                // address does not exist
                // create the address
                $createAddrCmd = $this->commandFactory->createCommand(CreateAddress::class,
                    array($this->creditCard->getAddress()));

                $addressId = $createAddrCmd->execute($this->tableGateway, $this->mapper, $this->serializer,
                    $this->commandFactory);
            } else {
                // address exists
                // fetch address ID
                $loadAddrCmd = $this->commandFactory->createCommand(LoadAddress::class,
                    array($this->creditCard->getAddress()));

                $address = $loadAddrCmd->execute($this->tableGateway, $this->mapper, $this->serializer,
                    $this->commandFactory);

                $addressId = $address->getId();
            }

            // fetch the card type id
            $this->tableGateway->setTable('L_CreditCardType');
            $typeRow = $this->tableGateway->findBy('description', $this->creditCard->getCardType());

            if (count($typeRow) < 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find card type %s.',
                    $this->creditCard->getCardType()));
            }

            // create the credit card profile
            $this->tableGateway->setTable('CreditCard');

            $result = $this->tableGateway->create(array(
                'customerId' => $this->creditCard->getCustomerId(),
                'cardTypeId' => $typeRow[0]['id'],
                'accountNumber' => $this->creditCard->getCardNumber(),
                'expirationDate' => $this->creditCard->getExpirationDate(),
                'addressId' => $addressId,
                'firstName' => $this->creditCard->getFirstName(),
                'lastName' => $this->creditCard->getLastName()
            ));

            $this->tableGateway->connection->commit();

            return $result;

        } catch (\Exception $exception) {
            $this->tableGateway->connection->rollBack();

            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
		if (preg_match('/(.)+(UNIQUE_CREDIT_CARD)(.)+/', $exception->getMessage())){
	    		return new CreditCardExistsException( sprintf( 'The credit card account ending in %s exists. %s', substr( $this->creditCard->getAccountNumber(), - 4 ), $exception->getMessage() ), $exception->getCode(), $exception->getPrevious() );
        }

		return $exception;

    }

}