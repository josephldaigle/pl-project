<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/1/18
 * Time: 10:53 AM
 */


namespace PapaLocal\Billing\Service;


use PapaLocal\Billing\Exception\AccountNotFoundException;
use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\Entity\User;
use PapaLocal\Billing\ValueObject\RechargeSetting;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * TransactionManager.
 *
 * Implement a service facade for clients to use when processing transactions.
 *
 * @package PapaLocal\Billing\Service
 */
class TransactionManager
{
	/**
	 * @var AuthorizeDotNet
	 */
	private $authorizeDotNet;

	/**
	 * @var TransactionRepository
	 */
	private $transactionRepository;

	/**
	 * @var BillingProfileRepository
	 */
	private $billingProfileRepository;

	/**
	 * @var SerializerInterface
	 */
	private $serializer;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

    /**
     * TransactionManager constructor.
     *
     * @param TransactionRepository    $transactionRepository
     * @param AuthorizeDotNet          $authorizeDotNet
     * @param BillingProfileRepository $billingProfileRepository
     * @param SerializerInterface      $serializer
     * @param LoggerInterface          $logger
     */
	public function __construct(TransactionRepository $transactionRepository,
                                AuthorizeDotNet $authorizeDotNet,
                                BillingProfileRepository $billingProfileRepository,
                                SerializerInterface $serializer,
                                LoggerInterface $logger)
    {
        $this->authorizeDotNet = $authorizeDotNet;
        $this->transactionRepository = $transactionRepository;
        $this->billingProfileRepository = $billingProfileRepository;
        $this->serializer = $serializer;
        $this->logger = $logger;
	}

	/**
	 * Charge a user's credit card.
	 *
	 * @param User                $user
	 * @param CreditCardInterface $creditCard
	 * @param float               $amount
	 * @param string              $description
	 *
	 * @throws AccountNotFoundException
	 * @throws ServiceOperationFailedException
	 */
	public function chargeCreditCard(User $user,
									CreditCardInterface $creditCard,
									float $amount,
									string $description)
	{
		$billingProfile = $this->billingProfileRepository->loadBillingProfile($user->getId(), true);

		if (! $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId())) {
			// the credit card does not have an id
			throw new AccountNotFoundException(sprintf('Credit card with id %s was not found in billing profile for customerId %s.', $creditCard->getId(), $billingProfile->getCustomerId()));
		}

		// process request against authnet
		$anetResult = $this->authorizeDotNet->chargeCreditCard($user->getUsername(), $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId()), $amount);

		$transaction = $this->serializer->denormalize(array(
			'userId' => $user->getId(),
			'billingProfileId' => $billingProfile->getId(),
			'amount' => $amount,
			'description' => $description,
			'payMethodId' => $creditCard->getId(),
			'aNetTransId' => $anetResult->getTransactionResponse()->getTransId(),
			'type' => Transaction::TYPE_CREDIT
		), Transaction::class, 'array');

		$result = $this->transactionRepository->saveSuccessfulTransaction($transaction);

		// set initial recharge setting
		if (is_null($billingProfile->getMinBalance()) || is_null($billingProfile->getMaxBalance())) {
			$rechargeSetting = $this->serializer->denormalize(array(
				'minBalance' => 50,
				'maxBalance' => 250,
			), RechargeSetting::class, 'array');
			$rechargeResult = $this->billingProfileRepository->saveRechargeSetting($user->getId(), $rechargeSetting);

			// user's auto deposit cannot be configured
			if (! $rechargeResult > 0) {
				$this->logger->debug(sprintf('Unable to save user %s\'s recharge settings: minBal [0.2%f], maxBal [0.2%f]', $user->getUsername(), $amount, $billingProfile->getBalance()));
			}
		}

		if (! $result > 0) {
			throw new ServiceOperationFailedException('Unable to save transaction to database.');
		}
	}
}