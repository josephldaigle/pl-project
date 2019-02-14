<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/13/18
 * Time: 8:45 PM
 */

namespace PapaLocal\AuthorizeDotNet;

use net\authorize\api\contract\v1\BankAccountType;
use net\authorize\api\contract\v1\CreateCustomerPaymentProfileRequest;
use net\authorize\api\contract\v1\CreateCustomerProfileRequest;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerPaymentProfileType;
use net\authorize\api\contract\v1\CustomerProfileExType;
use net\authorize\api\contract\v1\CustomerProfilePaymentType;
use net\authorize\api\contract\v1\CustomerProfileType;
use net\authorize\api\contract\v1\DeleteCustomerPaymentProfileRequest;
use net\authorize\api\contract\v1\DeleteCustomerProfileRequest;
use net\authorize\api\contract\v1\GetCustomerPaymentProfileListRequest;
use net\authorize\api\contract\v1\GetCustomerProfileIdsRequest;
use net\authorize\api\contract\v1\GetCustomerProfileRequest;
use net\authorize\api\contract\v1\PaymentProfileType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\UpdateCustomerProfileRequest;
use net\authorize\api\controller\CreateCustomerPaymentProfileController;
use net\authorize\api\controller\CreateCustomerProfileController;
use net\authorize\api\controller\CreateTransactionController;
use net\authorize\api\controller\DeleteCustomerPaymentProfileController;
use net\authorize\api\controller\DeleteCustomerProfileController;
use net\authorize\api\controller\GetCustomerPaymentProfileListController;
use net\authorize\api\controller\GetCustomerProfileController;
use net\authorize\api\controller\GetCustomerProfileIdsController;
use net\authorize\api\controller\UpdateCustomerProfileController;

/**
 * AuthorizeDotNetFactory.
 *
 * Creates Authorize.net library objects.
 */
class AuthorizeDotNetFactory
{
    /**
     * @return CustomerProfileType
     */
    public function getCustomerProfileType()
    {
        return new CustomerProfileType();
    }

    /**
     * @return CustomerPaymentProfileType
     */
    public function getCustomerPaymentProfileType()
    {
        return new CustomerPaymentProfileType();
    }

    /**
     * @return CustomerAddressType
     */
    public function getCustomerAddressType()
    {
        return new CustomerAddressType();
    }

    /**
     * @return CreateCustomerProfileRequest
     */
    public function getCreateCustomerProfileRequest()
    {
        return new CreateCustomerProfileRequest();
    }

    /**
     * @param CreateCustomerProfileRequest $request
     * @return CreateCustomerProfileController
     */
    public function getCreateCustomerProfileController(CreateCustomerProfileRequest $request)
    {
        return new CreateCustomerProfileController($request);
    }

    /**
     * @return GetCustomerProfileRequest
     */
    public function getGetCustomerProfileRequest()
    {
        return new GetCustomerProfileRequest();
    }

    /**
     * @param GetCustomerProfileRequest $request
     * @return GetCustomerProfileController
     */
    public function getGetCustomerProfileController(GetCustomerProfileRequest $request)
    {
        return new GetCustomerProfileController($request);
    }

    /**
     * @return GetCustomerProfileIdsRequest
     */
    public function getGetCustomerProfileIdsRequest()
    {
        return new GetCustomerProfileIdsRequest();
    }

    /**
     * @param GetCustomerProfileIdsRequest $request
     * @return GetCustomerProfileIdsController
     */
    public function getGetCustomerProfileIdsController(GetCustomerProfileIdsRequest $request)
    {
        return new GetCustomerProfileIdsController($request);
    }

    /**
     * @return CustomerProfileExType
     */
    public function getCustomerProfileExType()
    {
        return new CustomerProfileExType();
    }

    /**
     * @return UpdateCustomerProfileRequest
     */
    public function getUpdateCustomerProfileRequest()
    {
        return new UpdateCustomerProfileRequest();
    }

    /**
     * @param UpdateCustomerProfileRequest $request
     * @return UpdateCustomerProfileController
     */
    public function getUpdateCustomerProfileController(UpdateCustomerProfileRequest $request)
    {
        return new UpdateCustomerProfileController($request);
    }

    /**
     * @return DeleteCustomerProfileRequest
     */
    public function getDeleteCustomerProfileRequest()
    {
        return new DeleteCustomerProfileRequest();
    }

    /**
     * @param DeleteCustomerProfileRequest $request
     * @return DeleteCustomerProfileController
     */
    public function getDeleteCustomerProfileController(DeleteCustomerProfileRequest $request)
    {
        return new DeleteCustomerProfileController($request);
    }

    /**
     * @return CustomerProfilePaymentType
     */
    public function getCustomerProfilePaymentType()
    {
        return new CustomerProfilePaymentType();
    }

    /**
     * @return CreateCustomerPaymentProfileRequest
     */
    public function getCreateCustomerPaymentProfileRequest()
    {
        return new CreateCustomerPaymentProfileRequest();
    }

    /**
     * @param CreateCustomerPaymentProfileRequest $request
     * @return CreateCustomerPaymentProfileController
     */
    public function getCreateCustomerPaymentProfileController(CreateCustomerPaymentProfileRequest $request)
    {
        return new CreateCustomerPaymentProfileController($request);
    }

    /**
     * @return CreditCardType
     */
    public function getCreditCardType()
    {
        return new CreditCardType();
    }

    /**
     * @return BankAccountType
     */
    public function getBankAccountType()
    {
        return new BankAccountType();
    }

    /**
     * @return PaymentType
     */
    public function getPaymentType()
    {
        return new PaymentType();
    }

    /**
     * @return PaymentProfileType
     */
    public function getPaymentProfileType()
    {
        return new PaymentProfileType();
    }

    /**
     * @return GetCustomerPaymentProfileListRequest
     */
    public function getGetCustomerPaymentProfileListRequest()
    {
        return new GetCustomerPaymentProfileListRequest();
    }

    /**
     * @param GetCustomerPaymentProfileListRequest $request
     * @return GetCustomerPaymentProfileListController
     */
    public function getGetCustomerPaymentProfileListController(GetCustomerPaymentProfileListRequest $request)
    {
        return new GetCustomerPaymentProfileListController($request);
    }

    /**
     * @return DeleteCustomerPaymentProfileRequest
     */
    public function getDeleteCustomerPaymentProfileRequest()
    {
        return new DeleteCustomerPaymentProfileRequest();
    }

    /**
     * @param DeleteCustomerPaymentProfileRequest $request
     * @return DeleteCustomerPaymentProfileController
     */
    public function getDeleteCustomerPaymentProfileController(DeleteCustomerPaymentProfileRequest $request)
    {
        return new DeleteCustomerPaymentProfileController($request);
    }

    /**
     * @return TransactionRequestType
     */
    public function getTransactionRequestType()
    {
        return new TransactionRequestType();
    }

    /**
     * @return CreateTransactionRequest
     */
    public function getCreateTransactionRequest()
    {
        return new CreateTransactionRequest();
    }

    /**
     * @param CreateTransactionRequest $request
     * @return CreateTransactionController
     */
    public function getCreateTransactionController(CreateTransactionRequest $request)
    {
        return new CreateTransactionController($request);
    }
}