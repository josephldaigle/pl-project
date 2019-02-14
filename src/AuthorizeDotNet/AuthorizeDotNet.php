<?php
/**
 * Created by eWebify, LLC.
 * Date: 12/13/17
 */


namespace PapaLocal\AuthorizeDotNet;


use net\authorize\api\contract\v1\CustomerProfilePaymentType;
use net\authorize\api\contract\v1\PaymentProfileType;
use PapaLocal\Billing\Entity\BankAccountInterface;
use PapaLocal\Billing\Exception\DuplicateTransactionException;
use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Entity\Exception\AuthorizeDotNetOperationException;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\CustomerPaymentProfileMaskedType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use Psr\Log\LoggerInterface;


/**
 * Class AuthorizeDotNet.
 *
 * Client interface for Authorize.net API.
 *
 * The Authorize.net API does not prevent overwriting data stored on their servers, if updates
 * are called with blank or modified values for existing properties. When updating profiles stored
 * on Authorize.net servers, send a GET request for the profile first, then update the returned
 * object with the new parameters, and send that object with the update request.
 */
class AuthorizeDotNet implements AuthorizeDotNetInterface
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var MerchantAuthenticationType
     */
    private $authentication;

    /**
     * @var AuthorizeDotNetFactory
     */
    private $factory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthorizeDotNet constructor.
     *
     * @param string                     $environment
     * @param MerchantAuthenticationType $authentication
     * @param AuthorizeDotNetFactory     $factory
     * @param LoggerInterface            $logger
     */
    public function __construct(string $environment, MerchantAuthenticationType $authentication,
                                AuthorizeDotNetFactory $factory, LoggerInterface $logger)
    {
        $this->environment = $environment;
        $this->authentication = $authentication;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function createCustomerProfile(string $firstName, string $lastName, string $emailAddress)
    {
        // create customer profile obj
        $profile = $this->factory->getCustomerProfileType();
        $profile->setDescription($lastName . ', ' . $firstName);
        $profile->setEmail($emailAddress);

        // create and assemble request
        $request = $this->factory->getCreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setProfile($profile);

        // create the controller
        $controller = $this->factory->getCreateCustomerProfileController($request);

        $response = $this->execute($controller);

        // handle response
        if ($this->responseOk($response)) {
            // return customer Id
            return $response->getCustomerProfileId();
        } else {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getMessages()->getMessage()[0]->getText()));
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchCustomerProfile(string $username)
    {
        // create request
        $request = $this->factory->getGetCustomerProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setEmail($username);

        // execute request
        $controller = $this->factory->getGetCustomerProfileController($request);
        $response = $this->execute( $controller );

        // handle response
        if ($this->responseOk($response)) {
            return $response;
        } else {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getMessages()->getMessage()[0]->getText()));
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchCustomerProfileIds()
    {
        // create request obj
        $request = $this->factory->getGetCustomerProfileIdsRequest();
        $request->setMerchantAuthentication($this->authentication);

        // execute request
        $controller = $this->factory->getGetCustomerProfileIdsController($request);
        $response = $this->execute($controller);

        // handle response
        if ($this->responseOk($response)) {
            return $response->getIds();
        } else {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getMessages()->getMessage()[0]->getText()));
            return false;
        }
    }


    /**
     * Username is the only property that can be changed.
     *
     * @inheritDoc
     */
    public function updateCustomerProfile(int $id, string $username)
    {
        $profile = $this->factory->getCustomerProfileExType();
        $profile->setCustomerProfileId($id);
        $profile->setEmail($username);

        $request = $this->factory->getUpdateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setProfile($profile);

        $controller = $this->factory->getUpdateCustomerProfileController($request);

        $response = $this->execute($controller);

        // handle response
        if ($this->responseOk($response)) {
            return $response;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerProfile(int $profileId)
    {
        // create request obj
        $request = $this->factory->getDeleteCustomerProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setCustomerProfileId($profileId);

        // create controller
        $controller = $this->factory->getDeleteCustomerProfileController($request);

        $response = $this->execute($controller);

        // return response
        return ($this->responseOk($response));
    }

    /**
     * @inheritdoc
     */
    public function createCreditCardProfile(int $authNetCustId, CreditCardInterface $creditCard)
    {
        // build credit card obj
        $ccType = $this->factory->getCreditCardType();
        $ccType->setCardNumber($creditCard->getCardNumber());
        $ccType->setExpirationDate($creditCard->getExpirationDate());
        $ccType->setCardCode($creditCard->getSecurityCode());

        $paymentType = $this->factory->getPaymentType();
        $paymentType->setCreditCard($ccType);

        // create billing address for new payment type
        $custAddrType = $this->factory->getCustomerAddressType();
        $custAddrType->setAddress($creditCard->getAddress()->getStreetAddress());
        $custAddrType->setCity($creditCard->getAddress()->getCity());
        $custAddrType->setState($creditCard->getAddress()->getState());
        $custAddrType->setZip($creditCard->getAddress()->getPostalCode());
        $custAddrType->setCountry($creditCard->getAddress()->getCountry());

        // build payment profile obj
        $custPayProfType = $this->factory->getCustomerPaymentProfileType();
        $custPayProfType->setCustomerType('individual');
        $custPayProfType->setBillTo($custAddrType);
        $custPayProfType->setPayment($paymentType);

        if ($creditCard->isDefaultPayMethod()) {
            $custPayProfType->setDefaultPaymentProfile(true);
        }

        // create request obj
        $request = $this->factory->getCreateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setCustomerProfileId($authNetCustId);
        $request->setPaymentProfile($custPayProfType);
        $request->setValidationMode($this->getApiMode());

        // create controller
        $controller = $this->factory->getCreateCustomerPaymentProfileController($request);

        $response = $this->execute($controller);

        // return response
        if (! $this->responseOk($response)) {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getMessages()->getMessage()[0]->getText()), array('request' => array(
                    'customer_profile_id' => $request->getCustomerProfileId()
            )));

            throw new AuthorizeDotNetOperationException(sprintf('AuthorizeNet response: %s', $response->getMessages()->getMessage()[0]->getText()));

        }

        $this->logger->debug(sprintf('Payment profile added for customer %s: %s', $request->getCustomerProfileId(), substr($creditCard->getAccountNumber(), -4)));

        return ($this->responseOk($response));

    }

    /**
     * Fetches a user's credit card profile by matching the provided $creditCard detail to payment
     * methods stored in the user's Authorize.net profile.
     *
     * @param string              $username
     * @param CreditCardInterface $creditCard
     *
     * @return CustomerPaymentProfileMaskedType | false
     */
    public function fetchCreditCardProfile(string $username, CreditCardInterface $creditCard)
    {
        // fetch user's payment profiles
        $custProfile = $this->fetchCustomerProfile($username);
        if ($custProfile === false) {
            // no profile exists
            return false;
        }

        $payProfiles = $custProfile->getProfile()->getPaymentProfiles();

        $paymentProfile = null;
        foreach ($payProfiles as $profile) {

        	$ccType = $profile->getPayment()->getCreditCard();
        	$cardNumber = str_replace('X', '', $ccType->getCardNumber());

        	if ($cardNumber == $creditCard->getCardNumber()) {
        		// set customer profile
		        $profile->setCustomerProfileId($custProfile->getProfile()->getCustomerProfileId());
                return $profile;
	        }

        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function deleteCreditCardProfile(string $username, CreditCardInterface $creditCard)
    {
        $profile = $this->fetchCreditCardProfile($username, $creditCard);

        if ($profile === false) {
            // no payment profile exists
	        $this->logger->debug(sprintf('Could not find credit card profile for %s', $username), array('credit_card' => $creditCard));
            return true;
        }

        // create request obj
        $request = $this->factory->getDeleteCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setCustomerProfileId($profile->getCustomerProfileId());
        $request->setCustomerPaymentProfileId($profile->getCustomerPaymentProfileId());

        // create controller
        $controller = $this->factory->getDeleteCustomerPaymentProfileController($request);

        // delete the profile
        $response = $this->execute($controller);

        // return response
	    if (! $this->responseOk($response)) {
	    	$this->logger->error(sprintf('Unable to delete payment profile for %s', $username), array('credit_card' => $creditCard));

		    throw new AuthorizeDotNetOperationException($response->getTransactionResponse()->getErrors()[0]->getErrorText());
	    } else {
	    	$this->logger->debug(sprintf('Deleted payment profile for %s', $username), array('credit_card' => $creditCard, 'response' => $response->getMessages()->getMessage()[0]->getText()));
	    	return $this->responseOk($response);
	    }

    }

    /**
     * @inheritDoc
     */
    public function chargeCreditCard(string $username, CreditCardInterface $creditCard, float $amount)
    {
        // fetch the user's billing profile
        $custProf = $this->fetchCreditCardProfile($username, $creditCard);

        // return false if no cust profile found
        if ($custProf === false) {
        	$this->logger->debug('Unable to fetch billing profile.', array('username' => $username, 'creditCard' => $creditCard, 'response' => $custProf));
            throw new \AuthorizeNetException(sprintf("Unable to process transaction for %s. The user does not have a billing profile in AuthorizeNet.", $username));
        }

        // create profile obj
        $profile = $this->factory->getCustomerProfilePaymentType();
        $profile->setCustomerProfileId($custProf->getCustomerProfileId());

        $payProfType = $this->factory->getPaymentProfileType();
        $payProfType->setPaymentProfileId($custProf->getCustomerPaymentProfileId());
        $profile->setPaymentProfile($payProfType);

        // create trans req obj
        $transReqType = $this->factory->getTransactionRequestType();
        $transReqType->setTransactionType('authCaptureTransaction');
        $transReqType->setAmount($amount);
        $transReqType->setProfile($profile);

        // create request obj
        $request = $this->factory->getCreateTransactionRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setTransactionRequest($transReqType);

        // create controller
        $controller = $this->factory->getCreateTransactionController($request);

        // call api
        $response = $this->execute($controller);

        // handle response
        if ($this->responseOk($response)) {
        	$this->logger->debug(sprintf('Credit card profile charged for %s', $username), array(
        		'credit_card' => $creditCard,
		        'amount' => $amount));
            return $response;
        } else {
        	switch ($response->getTransactionResponse()->getErrors()[0]->getErrorCode()) {
		        case 11: // duplicate transactions
						throw new DuplicateTransactionException(sprintf('Unable to process duplicate transaction for %s.', $username));
			        break;
		        default: // unexpected exception
			        $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
				        $response->getTransactionResponse()->getErrors()[0]->getErrorText()),
				        array('errorCode' => $response->getTransactionResponse()->getErrors()[0]->getErrorCode()));

			        throw new AuthorizeDotNetOperationException(sprintf('Unable to charge card: %s',
				        $response->getTransactionResponse()->getErrors()[0]->getErrorText()));
	        }

        }
    }

    /**
     * @inheritDoc
     */
    public function refundCreditCard(string $username, CreditCardInterface $creditCard, float $amount)
    {
        // create payment obj
        $ccType = $this->factory->getCreditCardType();
        $ccType->setCardNumber($creditCard->getCardNumber());
        $ccType->setExpirationDate($creditCard->getExpirationDate());
        $paymentType = $this->factory->getPaymentType();
        $paymentType->setCreditCard($ccType);

        // create request type obj
        $transactionRequest = $this->factory->getTransactionRequestType();
        $transactionRequest->setTransactionType( "refundTransaction");
        $transactionRequest->setAmount($amount);
        $transactionRequest->setPayment($paymentType);

        // create transaction request
        $request = $this->factory->getCreateTransactionRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setTransactionRequest( $transactionRequest);

        // create controller
        $controller = $this->factory->getCreateTransactionController($request);

        // call api
        $response = $controller->executeWithApiResponse( $this->environment);

        // handle response
        if ($this->responseOk($response)) {
	        $this->logger->debug(sprintf('Credit card profile charged for %s', $username), array(
		        'credit_card' => $creditCard,
		        'amount' => $amount));
            return $response;
        } else {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getTransactionResponse()->getErrors()[0]->getErrorText()),
                array('errorCode' => $response->getTransactionResponse()->getErrors()[0]->getErrorCode()));

            throw new \LogicException(sprintf('Unable to process refund: %s',
                $response->getTransactionResponse()->getErrors()[0]->getErrorText()));
        }
    }

    /**
     * @param string               $authNetCustId
     * @param BankAccountInterface $bankAccount
     *
     * @return bool
     * @throws AuthorizeDotNetOperationException
     */
    public function createBankAccountProfile(string $authNetCustId, BankAccountInterface $bankAccount)
    {
        // build credit card obj
        $bankAccountType = $this->factory->getBankAccountType();
        $bankAccountType->setAccountNumber($bankAccount->getAccountNumber());
        $bankAccountType->setRoutingNumber($bankAccount->getRoutingNumber());
        $bankAccountType->setAccountType('checking');
        $bankAccountType->setBankName($bankAccount->getBankName());
        $bankAccountType->setNameOnAccount($bankAccount->getAccountHolder());
        $bankAccountType->setEcheckType('PPD');

        $paymentType = $this->factory->getPaymentType();
        $paymentType->setBankAccount($bankAccountType);

        // create billing address for new payment type
        $custAddrType = $this->factory->getCustomerAddressType();
        $custAddrType->setFirstName($bankAccount->getFirstName());
        $custAddrType->setLastName($bankAccount->getLastName());
        $custAddrType->setAddress($bankAccount->getAddress()->getStreetAddress());
        $custAddrType->setCity($bankAccount->getAddress()->getCity());
        $custAddrType->setState($bankAccount->getAddress()->getState());
        $custAddrType->setZip($bankAccount->getAddress()->getPostalCode());
        $custAddrType->setCountry($bankAccount->getAddress()->getCountry());

        // build payment profile obj
        $custPayProfType = $this->factory->getCustomerPaymentProfileType();
        $custPayProfType->setCustomerType('individual');
        $custPayProfType->setBillTo($custAddrType);
        $custPayProfType->setPayment($paymentType);

        if ($bankAccount->isDefaultPayMethod()) {
            $custPayProfType->setDefaultPaymentProfile(true);
        }

        // create request obj
        $request = $this->factory->getCreateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setCustomerProfileId($authNetCustId);
        $request->setPaymentProfile($custPayProfType);
        $request->setValidationMode($this->getApiMode());

        // create controller
        $controller = $this->factory->getCreateCustomerPaymentProfileController($request);

        $response = $this->execute($controller);

        // return response
        if (! $this->responseOk($response)) {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getMessages()->getMessage()[0]->getText()), array('request' => array(
                'customer_profile_id' => $request->getCustomerProfileId()
            )));

            throw new AuthorizeDotNetOperationException(sprintf('AuthorizeNet response: %s', $response->getMessages()->getMessage()[0]->getText()));

        }

        $this->logger->debug(sprintf('Payment profile added for customer %s: %s', $request->getCustomerProfileId(), substr($bankAccount->getAccountNumber(), -4)));

        return ($this->responseOk($response));
    }

    /**
     * @param string               $username
     * @param BankAccountInterface $bankAccount
     *
     * @return bool
     */
    public function fetchBankAccountProfile(string $username, BankAccountInterface $bankAccount)
    {
        // fetch user's payment profiles from AuthNet
        $custProfile = $this->fetchCustomerProfile($username);
        if ($custProfile === false) {
            // no profile exists, or not found
            return false;
        }

        // extract payment profiles from AuthNet Repsonse
        $payProfiles = $custProfile->getProfile()->getPaymentProfiles();

        // find the selected account in the authNet list
        foreach ($payProfiles as $profile) {

            // extract the credit card type from the payment profile
            $bankAccountType = $profile->getPayment()->getBankAccount();

            // reduce the account number to match the format stored in database
            $accountNumber = substr($bankAccountType->getAccountNumber(), (strlen($bankAccountType->getAccountNumber()) - 4));

            // if the account matches, return the profile
            if ($accountNumber == $bankAccount->getAccountNumber()) {
                // set customer profile id
                $profile->setCustomerProfileId($custProfile->getProfile()->getCustomerProfileId());
                return $profile;
            }
        }

        return false;
    }

    public function deleteBankAccountProfile(string $username, BankAccountInterface $bankAccount)
    {
        $profile = $this->fetchBankAccountProfile($username, $bankAccount);

        if ($profile === false) {
            // no payment profile exists
            $this->logger->debug(sprintf('Could not find bank account profile for %s', $username), array('bank_account' => $bankAccount));
            return true;
        }

        // create request obj
        $request = $this->factory->getDeleteCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setCustomerProfileId($profile->getCustomerProfileId());
        $request->setCustomerPaymentProfileId($profile->getCustomerPaymentProfileId());

        // create controller
        $controller = $this->factory->getDeleteCustomerPaymentProfileController($request);

        // delete the profile
        $response = $this->execute($controller);

        // return response
        if (! $this->responseOk($response)) {
            $this->logger->error(sprintf('Unable to delete payment profile for %s', $username), array('bank_account' => $bankAccount));

            throw new AuthorizeDotNetOperationException($response->getTransactionResponse()->getErrors()[0]->getErrorText());
        } else {
            $this->logger->debug(sprintf('Deleted payment profile for %s', $username), array('bank_account' => $bankAccount, 'response' => $response->getMessages()->getMessage()[0]->getText()));
            return $this->responseOk($response);
        }
    }

    /**
     * @param string               $username
     * @param BankAccountInterface $bankAccount
     * @param float                $amount
     *
     * @return mixed
     * @throws AuthorizeDotNetOperationException
     * @throws \AuthorizeNetException
     */
    public function chargeBankAccount(string $username, BankAccountInterface $bankAccount, float $amount)
    {
        // fetch the user's billing profile
        $custProf = $this->fetchBankAccountProfile($username, $bankAccount);

        // return false if no cust profile found
        if ($custProf === false) {
            $this->logger->debug('Unable to fetch billing profile.', array('username' => $username, 'bankAccount' => $bankAccount, 'response' => $custProf));
            throw new \AuthorizeNetException(sprintf("Unable to process transaction for %s. The user does not have a billing profile in AuthorizeNet.", $username));
        }

        // create profile obj
        $profile = $this->factory->getCustomerProfilePaymentType();
        $profile->setCustomerProfileId($custProf->getCustomerProfileId());

        $payProfType = $this->factory->getPaymentProfileType();
        $payProfType->setPaymentProfileId($custProf->getCustomerPaymentProfileId());
        $profile->setPaymentProfile($payProfType);

        // create trans req obj
        $transReqType = $this->factory->getTransactionRequestType();
        $transReqType->setTransactionType('authCaptureTransaction');
        $transReqType->setAmount($amount);
        $transReqType->setProfile($profile);

        // create request obj
        $request = $this->factory->getCreateTransactionRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setTransactionRequest($transReqType);

        // create controller
        $controller = $this->factory->getCreateTransactionController($request);

        // call api
        $response = $this->execute($controller);

        // handle response
        if ($this->responseOk($response)) {
            $this->logger->debug(sprintf('Bank account profile charged for %s', $username), array(
                'bank_account' => $bankAccount,
                'amount' => $amount));
            return $response;
        } else {
            switch ($response->getTransactionResponse()->getErrors()[0]->getErrorCode()) {
                case 11: // duplicate transactions
                    throw new DuplicateTransactionException(sprintf('Unable to process duplicate transaction for %s.', $username));
                    break;
                default: // unexpected exception
                    $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                        $response->getTransactionResponse()->getErrors()[0]->getErrorText()),
                        array('errorCode' => $response->getTransactionResponse()->getErrors()[0]->getErrorCode()));

                    throw new AuthorizeDotNetOperationException(sprintf('Unable to charge account: %s',
                        $response->getTransactionResponse()->getErrors()[0]->getErrorText()));
            }
        }
    }

    /**
     * @param string               $username
     * @param BankAccountInterface $bankAccount
     * @param float                $amount
     *
     * @return AnetApiResponseType
     * @throws \AuthorizeNetException
     * @throws \LogicException
     */
    public function refundBankAccount(string $username, BankAccountInterface $bankAccount, float $amount)
    {
        // fetch the user's billing profile
        $bankAcctProfile = $this->fetchBankAccountProfile($username, $bankAccount);

        // return false if no cust profile found
        if ($bankAcctProfile === false) {
            $this->logger->debug('Unable to fetch billing profile.', array('username' => $username, 'bankAccount' => $bankAccount, 'response' => $bankAcctProfile));
            throw new \AuthorizeNetException(sprintf("Unable to process transaction for %s. The user does not have a billing profile in AuthorizeNet.", $username));
        }

        $profileType = new CustomerProfilePaymentType();
        $paymentProfileType = new PaymentProfileType();
        $paymentProfileType->setPaymentProfileId($bankAcctProfile->getCustomerPaymentProfileId());
        $profileType->setPaymentProfile($paymentProfileType);
        $profileType->setCustomerProfileId($bankAcctProfile->getCustomerProfileId());

        // create request type obj
        $transactionRequest = $this->factory->getTransactionRequestType();
        $transactionRequest->setTransactionType( "refundTransaction");
        $transactionRequest->setProfile($profileType);
        $transactionRequest->setAmount($amount);

        // create transaction request
        $request = $this->factory->getCreateTransactionRequest();
        $request->setMerchantAuthentication($this->authentication);
        $request->setTransactionRequest( $transactionRequest);

        // create controller
        $controller = $this->factory->getCreateTransactionController($request);

        // call api
        $response = $controller->executeWithApiResponse( $this->environment);

        // handle response
        if ($this->responseOk($response)) {
            $this->logger->debug(sprintf('Bank account profile refunded for %s', $username), array(
                'bank_account' => $bankAccount,
                'amount' => $amount));

            return $response;
        } else {
            $this->logger->debug(sprintf('Error response from Authorize.Net: %s',
                $response->getTransactionResponse()->getErrors()[0]->getErrorText()),
                array('errorCode' => $response->getTransactionResponse()->getErrors()[0]->getErrorCode()));

            throw new \LogicException(sprintf('Unable to process refund: %s',
                $response->getTransactionResponse()->getErrors()[0]->getErrorText()));
        }
    }

    /**
     * Returns the mode the API should be executed in. Some requests require this field.
     * Defaults to testMode.
     *
     * @return string liveMode if environment set to PRODUCTION, else testMode
     */
    public function getApiMode()
    {
        return ($this->environment === ANetEnvironment::PRODUCTION) ? 'liveMode' : 'testMode';
    }

    /**
     * Checks whether or not the response is valid (as defined by Authorize.net API docs.
     *
     * @param ANetApiResponseType $response
     * @return bool
     */
    private function responseOk(ANetApiResponseType $response): bool
    {
        return (($response->getMessages()->getResultCode() === 'Ok')
            && ($response->getmessages()->getMessage()[0]->getText() === 'Successful.'));
    }

    /**
     * Executes a request to the Authorize.Net API.
     *
     * @param $controller
     * @return mixed
     * @throws \Exception
     */
    protected function execute($controller)
    {
        try {
            return $controller->executeWithApiResponse($this->environment);
        } catch (\Exception $exception) {
            $this->logger->critical('An error occurred interacting with the Authorize.net api.', array('exception' => $exception));
            throw $exception;
        }
    }
}