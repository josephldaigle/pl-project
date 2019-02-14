<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/16/18
 * Time: 12:57 PM
 */


namespace PapaLocal\Billing\Service;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Billing\Data\BillingProfileHydrator;
use PapaLocal\Billing\Entity\BankAccountInterface;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Exception\BillingProfileOperationException;
use PapaLocal\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class BillingProfileManager.
 *
 * Service class for interacting with a user's billing profiles.
 *
 * This class helps ensure the Authorize.net system and PapaLocal stay in sync.
 *
 * @package PapaLocal\Billing\Service
 */
class BillingProfileManager
{
    /**
     * @var BillingProfileRepository
     */
	private $billingProfileRepository;

	/**
	 * @var BillingProfileHydrator
	 */
	private $billingProfileHydrator;

    /**
     * @var AuthorizeDotNet
     */
    private $authorizeNet;

	/**
	 * @var SerializerInterface;
	 */
	private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BillingProfileManager constructor.
     *
     * @param BillingProfileRepository $billingProfileRepository
     * @param BillingProfileHydrator   $billingProfileHydrator
     * @param AuthorizeDotNet          $authorizeNet
     * @param SerializerInterface      $serializer
     * @param LoggerInterface          $logger
     */
    public function __construct(BillingProfileRepository $billingProfileRepository,
	                            BillingProfileHydrator $billingProfileHydrator,
                                AuthorizeDotNet $authorizeNet,
                                SerializerInterface $serializer,
                                LoggerInterface $logger)
    {
        $this->billingProfileRepository = $billingProfileRepository;
        $this->billingProfileHydrator = $billingProfileHydrator;
        $this->authorizeNet = $authorizeNet;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function saveCreditCardForUser(User $user, CreditCard $creditCard)
    {
        if (! $this->billingProfileRepository->hasActiveBillingProfile($user->getId())) {
            // make one
            if ($this->billingProfileRepository->hasBillingProfile($user->getId())) {
                $this->billingProfileRepository->activateBillingProfile($user->getId());
            } else {
                $this->createBillingProfile($user);
            }
        }

        // bill prof exists now.

        // user has a billing profile in the database
        $billingProfile = $this->billingProfileRepository->loadBillingProfile($user->getId(), true);

	    try {

	    	// create credit card profile in AuthNet
	    	$this->authorizeNet->createCreditCardProfile($billingProfile->getCustomerId(), $creditCard);

	        // save the credit card to db
	        $creditCard->setCustomerId( $billingProfile->getCustomerId() );
	        $creditCard->setCardNumber( substr( $creditCard->getCardNumber(), - 4 ) );
	        $result = $this->billingProfileRepository->saveCreditCard( $user->getId(), $creditCard );

	        $this->logger->debug( sprintf( 'Credit card %s added for %s.', $creditCard->getAccountNumber(),
		        $user->getUsername() ) );

	        return $result;

        } catch (\Exception $exception) {

            // roll back authnet profile
            $this->authorizeNet->deleteCreditCardProfile($user->getUsername(), $creditCard);

            // log error creating AuthNet profile
            $this->logger->error(sprintf('An %s exception occurred at line %s of %s: %s',
                get_class($exception), $exception->getLine(), $exception->getFile(), $exception->getMessage()),
                array('trace' => $exception->getTrace()));

            throw $exception;
        }
    }

    /**
     * Create a user's bank account.
     *
     * @param User                 $user
     * @param BankAccountInterface $bankAccount
     *
     * @return mixed
     * @throws \Exception
     */
    public function saveBankAccount(User $user, BankAccountInterface $bankAccount)
    {
        if (! $this->billingProfileRepository->hasActiveBillingProfile($user->getId())) {
            // make one
            if ($this->billingProfileRepository->hasBillingProfile($user->getId())) {
                $this->billingProfileRepository->activateBillingProfile($user->getId());
            } else {
                $this->createBillingProfile($user);
            }
        }

        // bill prof exists now.

        // user has a billing profile in the database
        $billingProfile = $this->billingProfileRepository->loadBillingProfile($user->getId(), true);

        try {

            // create credit card profile in AuthNet
            $this->authorizeNet->createBankAccountProfile($billingProfile->getCustomerId(), $bankAccount);

            // save the credit card to db
            $bankAccount->setCustomerId( $billingProfile->getCustomerId() );
            $bankAccount->setAccountNumber( substr( $bankAccount->getAccountNumber(), - 4 ) );
            $result = $this->billingProfileRepository->saveBankAccount( $user->getId(), $bankAccount );

            $this->logger->debug( sprintf( 'Bank account %s added for %s.', $bankAccount->getAccountNumber(),
                $user->getUsername() ) );

            return $result;

        } catch (\Exception $exception) {

            // roll back authnet profile
            $this->authorizeNet->deleteBankAccountProfile($user->getUsername(), $bankAccount);

            // log error creating AuthNet profile
            $this->logger->error(sprintf('An %s exception occurred at line %s of %s: %s',
                get_class($exception), $exception->getLine(), $exception->getFile(), $exception->getMessage()),
                array('trace' => $exception->getTrace()));

            throw $exception;
        }
    }

    /**
     * Delete a user's credit card profile.
     *
     * @param User       $user
     * @param CreditCard $creditCard
     * @return int
     * @throws \PapaLocal\Entity\Exception\AuthorizeDotNetOperationException
     * @throws \PapaLocal\Entity\Exception\ServiceOperationFailedException
     */
	public function deleteCreditCard(User $user, CreditCard $creditCard)
	{
		// load user's billing profile
		$this->billingProfileHydrator->setEntity($this->serializer->denormalize(array('userId' => $user->getId()), BillingProfile::class, 'array'));
		$billingProfile = $this->billingProfileHydrator->hydrate();

		// delete payment profile
		if ($billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId())) {
			$authNetResponse = $this->authorizeNet->deleteCreditCardProfile($user->getUsername(),
				$billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId()));

			return $this->billingProfileRepository->deleteCreditCard($creditCard);
		}
	}

    /**
     * Creates a billing profile for the user.
     *
     * @param User $user
     * @return mixed
     * @throws BillingProfileOperationException
     */
    protected function createBillingProfile(User $user)
    {
        // create profile in auth.net
        $customerProfile = $this->authorizeNet->fetchCustomerProfile($user->getUsername());

        if (false === $customerProfile) {
            // user does not have profile in Authorize.Net, create one
            $customerId = $this->authorizeNet->createCustomerProfile($user->getPerson()->getFirstName(),
                $user->getPerson()->getLastName(), $user->getUsername());

            // create profile in Authorize.Net failed
            if (false === $customerId) {
                throw new BillingProfileOperationException(sprintf('Unable to create billing profile for %s'
                    . ' in Authorize.Net system.', $user->getUsername()));
            }

        } else {
            // user has an Authorize.Net profile, but no record in the database
            $customerId = $customerProfile->getProfile()->getCustomerProfileId();
        }

        try {
            // save billing profile to db
            return $this->billingProfileRepository->createBillingProfile($user->getId(), $customerId);

        } catch (\Exception $exception) {
            // database write failed

            // delete profile from AuthNet to avoid systems getting out of sync
            $authProfDeleted = $this->authorizeNet->deleteCustomerProfile($customerId);

            if (! $authProfDeleted) {
                // could not sync systems
                $this->logger->debug(sprintf('Failed deleting customer %s from Authorize.Net.'
                    . ' The profile must be manually deleted in order to keep system in sync.', $customerId),
                    array('username' => $user->getUsername()));
            }

            // log exception
            $this->logger->error($exception->getMessage(), array(
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            ));

            throw new BillingProfileOperationException(sprintf('Unable to save billing profile for %s. Customer id: %s',
                $user->getUsername(), $customerId));
        }
    }
}