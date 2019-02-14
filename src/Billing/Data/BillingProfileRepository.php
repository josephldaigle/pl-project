<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 3/28/18
 */


namespace PapaLocal\Billing\Data;


use PapaLocal\Data\Command\User\Billing\CreateCreditCardProfile;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\Billing\ValueObject\RechargeSetting;


/**
 * Class BillingProfileRepository.
 *
 * @package PapaLocal\Billing\Data
 */
class BillingProfileRepository extends AbstractRepository
{
    /**
     * @var BillingProfileHydrator
     */
    private $billingProfileHydrator;

	/**
	 * BillingProfileRepository constructor.
	 *
	 * {@inheritdoc}
	 *
	 * @param DataResourcePool       $dataResourcePool
	 * @param BillingProfileHydrator $billingUserContactDetailHydrator
	 */
    public function __construct(DataResourcePool $dataResourcePool,
                                BillingProfileHydrator $billingUserContactDetailHydrator)
    {
        parent::__construct($dataResourcePool);

        $this->billingProfileHydrator = $billingUserContactDetailHydrator;
    }


    /**
     * Check if a user has a billing profile.
     *
     * Does not guarantee the profile is active:
     * @see hasActiveBillingProfile
     *
     * @param int $userId
     * @return bool
     */
    public function hasBillingProfile(int $userId)
    {
        $this->tableGateway->setTable('BillingProfile');
        $profRows = $this->tableGateway->findBy('userId', $userId);

        return (count($profRows) > 0);
    }

	/**
	 * Check if a user has a default payment method.
	 *
	 * @param int $userId
	 *
	 * @return bool
	 */
    public function hasDefaultPayMethod(int $userId)
    {
    	$this->tableGateway->setTable('v_user_payment_profile');
    	$profRows = $this->tableGateway->findByColumns(array(
    		'userId' => $userId,
		    'defaultPayMethod' => 1
	    ));

    	return (count($profRows) > 0);
    }

    /**
     * Check if a user has an active billing profile.
     *
     * @param int $userId
     * @return bool
     */
    public function hasActiveBillingProfile(int $userId)
    {
        $this->tableGateway->setTable('BillingProfile');
        $profRows = $this->tableGateway->findBy('userId', $userId);

        return ((count($profRows) > 0) && (intval($profRows[0]['isActive']) === 1));
    }

	/**
	 * Create a user's billing profile.
	 *
	 * @param int $userId
	 * @param int $customerId
	 *
	 * @return int
	 * @throws \LogicException
	 */
    public function createBillingProfile(int $userId, int $customerId)
    {
        // check for existing billing profile
        if ($this->hasActiveBillingProfile($userId)) {
            // user has profile -- cannot create one
            throw new \LogicException(sprintf('User %s already has a billing profile.', $userId));

        } else {

            // create the user's billing profile
            $this->tableGateway->setTable('BillingProfile');

            return $this->tableGateway->create(array(
                'userId' => $userId,
                'customerId' => $customerId,
                'minBalance' => 50.00,
                'maxBalance' => 250.00,
                'isActive' => 1
            ));
        }
    }

    /**
     * Loads a user's billing profile.
     *
     * @param int  $userId
     * @param bool $includePayProfiles whether or not to load the user's payment profiles with the billing profile
     *
     * @return \PapaLocal\Entity\Entity
     *
     * @throws \InvalidArgumentException
     */
    public function loadBillingProfile(int $userId, $includePayProfiles = false)
    {
        if (! $this->hasActiveBillingProfile($userId)) {
            return $this->serializer->denormalize(array(), BillingProfile::class, 'array');
        }

        $this->tableGateway->setTable('v_user_billing_profile');
        $profileRows = $this->tableGateway->findBy('userId', $userId);

        $billingProfile = $this->serializer->denormalize($profileRows[0], BillingProfile::class, 'array');

        // load payment profile
        if ($includePayProfiles) {
            $this->billingProfileHydrator->setEntity($billingProfile);
            $billingProfile = $this->billingProfileHydrator->hydrate();
        }

        return $billingProfile;
    }

    /**
     * @param string $guid
     * @param bool   $includePayProfiles
     *
     * @return \PapaLocal\Entity\Entity
     */
    public function loadBillingProfileByUserGuid(string $guid, $includePayProfiles = false)
    {

        $this->tableGateway->setTable('v_user_billing_profile');
        $profileRows = $this->tableGateway->findBy('userGuid', $guid);

        $billingProfile = $this->serializer->denormalize($profileRows[0], BillingProfile::class, 'array');

        // load payment profile
        if ($includePayProfiles) {
            $this->billingProfileHydrator->setEntity($billingProfile);
            $billingProfile = $this->billingProfileHydrator->hydrate();
        }

        return $billingProfile;
    }


	/**
	 * De-activate a user's billing profile.
	 *
	 * @param int $userId
	 *
	 * @return int
	 * @throws \InvalidArgumentException
	 */
    public function deactivateBillingProfile(int $userId)
    {
        if (! $this->hasActiveBillingProfile($userId)) {
            throw new \InvalidArgumentException(sprintf('The user %s does not have a billing profile.', $userId));
        }

        // fetch row
        $this->tableGateway->setTable('BillingProfile');
        $billProRows = $this->tableGateway->findBy('userId', $userId);

        // update row data with new flag
        $billProRows[0]['isActive'] = 0;

        // save row
        return $this->tableGateway->update($billProRows[0]);
    }

	/**
	 * Activate a user's billing profile.
	 *
	 * @param int $userId
	 *
	 * @return int
	 * @throws \InvalidArgumentException
	 */
    public function activateBillingProfile(int $userId)
    {
        if (! $this->hasBillingProfile($userId)) {
            throw new \InvalidArgumentException(sprintf('The user %s does not have a billing profile.', $userId));
        }

        // fetch row
        $this->tableGateway->setTable('BillingProfile');
        $billProRows = $this->tableGateway->findBy('userId', $userId);

        // update row data with new flag
        $billProRows[0]['isActive'] = 1;

        // save row
        return $this->tableGateway->update($billProRows[0]);
    }

    /**
     * Check if a credit card profile exists.
     *
     * @param int $cardNumber
     * @return bool
     */
    public function creditCardExists(int $cardNumber): bool
    {
        $this->tableGateway->setTable('CreditCard');
        $ccRows = $this->tableGateway->findBy('accountNumber', $cardNumber);

        return (count($ccRows) > 0);
    }

    /**
     * Save a user's credit card profile.
     *
     * @param int                 $userId
     * @param CreditCardInterface $creditCard
     * @return mixed
     * @throws ServiceOperationFailedException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \ReflectionException
     */
    public function saveCreditCard(int $userId, CreditCardInterface $creditCard)
    {
        	$creditCard->setCardNumber(substr($creditCard->getCardNumber(), -4));
            $cmd = $this->commandFactory->createCommand(CreateCreditCardProfile::class, array($userId, $creditCard));
            $result = $cmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            $creditCard->setId($result);
            if ($creditCard->isDefaultPayMethod()) {
                $this->setAsDefaultPaymentMethod($creditCard);
            }

            return $result;
    }

    /**
     * Delete a credit card.
     *
     * @param CreditCard $creditCard
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function deleteCreditCard(CreditCard $creditCard)
    {
        $this->tableGateway->setTable('CreditCard');
        $ccRows = $this->tableGateway->findBy('id', $creditCard->getId());

        if (count($ccRows) < 1) {
            throw new ServiceOperationFailedException(sprintf('Unable to delete credit card (not found): %s',
                $creditCard->getCardNumber()));
        }

        return $this->tableGateway->delete($ccRows[0]['id']);
    }

    /**
     * Set card as default.
     *
     * Automatically handles cases where a primary is already set.
     *
     * @param CreditCard $creditCard
     * @return int
     * @throws ServiceOperationFailedException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function setAsDefaultPaymentMethod(CreditCard $creditCard)
    {
        // start transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            // fetch credit cards for customer
            $this->tableGateway->setTable('CreditCard');
            $ccRows = $this->tableGateway->findBy('customerId', $creditCard->getCustomerId());

            if (count($ccRows) < 1) {
                throw new \LogicException(sprintf('Unable to set default pay method for customer: %s, no cards found.',
                    $creditCard->getCustomerId()));
            }

            // strip existing defaults
            foreach($ccRows as $cardRow) {
                // unset current default
                if (intval($cardRow['defaultPayMethod']) === 1) {
                    // unset existing default pay methods
                    $cardRow['defaultPayMethod'] = 0;
                    $this->tableGateway->update($cardRow);
                }

                // set new default card
                if ($cardRow['id'] == $creditCard->getId()) {
                    $cardRow['defaultPayMethod'] = 1;
                    $this->tableGateway->update($cardRow);
                }
            }

            // commit changes
            $this->tableGateway->connection->commit();

            return true;

        } catch (\Exception $exception) {
            // roll back changes
            $this->tableGateway->connection->rollBack();

            throw new ServiceOperationFailedException(sprintf('Unable to set default pay method for customer %s: %s',
                $creditCard->getCustomerId(), $exception->getMessage()));
        }
    }

    /**
     * Update a user's auto recharge settings (how and when their account balance is refilled)
     *
     * @param int             $userId
     * @param RechargeSetting $rechargeSetting
     * @return int
     * @throws ServiceOperationFailedException
     */
	public function saveRechargeSetting(int $userId, RechargeSetting $rechargeSetting)
	{
		$this->tableGateway->setTable('BillingProfile');
		$billProfileRows = $this->tableGateway->findBy('userId', $userId);

		if (count($billProfileRows) !== 1) {
		    throw new ServiceOperationFailedException(
		        sprintf('Found %s billing profiles for %i when saving recharge settings.',
                count($billProfileRows), $userId));
        }

        $billProfileRows[0]['minBalance'] = $rechargeSetting->getMinBalance();
        $billProfileRows[0]['maxBalance'] = $rechargeSetting->getMaxBalance();

		return $this->tableGateway->update($billProfileRows[0]);
    }
}