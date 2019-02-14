<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/13/18
 * Time: 10:25 PM
 */

namespace Test\Unit\AuthorizeDotNet;

use PapaLocal\AuthorizeDotNet\AuthorizeDotNetFactory;
use net\authorize\api\contract\v1\CreateCustomerPaymentProfileRequest;
use net\authorize\api\contract\v1\CreateCustomerProfileRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerPaymentProfileType;
use net\authorize\api\contract\v1\CustomerProfileExType;
use net\authorize\api\contract\v1\CustomerProfileType;
use net\authorize\api\contract\v1\DeleteCustomerProfileRequest;
use net\authorize\api\contract\v1\GetCustomerProfileIdsRequest;
use net\authorize\api\contract\v1\GetCustomerProfileRequest;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\UpdateCustomerProfileRequest;
use net\authorize\api\controller\CreateCustomerPaymentProfileController;
use net\authorize\api\controller\CreateCustomerProfileController;
use net\authorize\api\controller\DeleteCustomerProfileController;
use net\authorize\api\controller\GetCustomerProfileController;
use net\authorize\api\controller\GetCustomerProfileIdsController;
use net\authorize\api\controller\UpdateCustomerProfileController;
use PHPUnit\Framework\TestCase;

/**
 * AuthorizeDotNetFactoryTest.
 *
 * Unit tests for AuthorizeDotNetFactory.
 */
class AuthorizeDotNetFactoryTest extends TestCase
{
    /**
     * @var AuthorizeDotNetFactory
     */
    private $factory;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new AuthorizeDotNetFactory();
    }

    public function testGetCustomerProfileTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCustomerProfileType();

        // make assertions
        $this->assertInstanceOf(CustomerProfileType::class, $class);
    }

    public function testGetCustomerAddressTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCustomerAddressType();

        // make assertions
        $this->assertInstanceOf(CustomerAddressType::class, $class);
    }

    public function testGetCreateCustomerProfileRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCreateCustomerProfileRequest();

        // make assertions
        $this->assertInstanceOf(CreateCustomerProfileRequest::class, $class);
    }

    public function testGetCreateCustomerProfileControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(CreateCustomerProfileRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getCreateCustomerProfileController($requestMock);

        // make assertions
        $this->assertInstanceOf(CreateCustomerProfileController::class, $class);
    }

    public function testGetGetCustomerProfileRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getGetCustomerProfileRequest();

        // make assertions
        $this->assertInstanceOf(GetCustomerProfileRequest::class, $class);
    }

    public function testGetGetCustomerProfileControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(GetCustomerProfileRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getGetCustomerProfileController($requestMock);

        // make assertions
        $this->assertInstanceOf(GetCustomerProfileController::class, $class);
    }

    public function testGetGetCustomerProfileIdsRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getGetCustomerProfileIdsRequest();

        // make assertions
        $this->assertInstanceOf(GetCustomerProfileIdsRequest::class, $class);
    }

    public function testGetGetCustomerProfileIdsControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(GetCustomerProfileIdsRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getGetCustomerProfileIdsController($requestMock);

        // make assertions
        $this->assertInstanceOf(GetCustomerProfileIdsController::class, $class);
    }

    public function testGetCustomerProfileExTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCustomerProfileExType();

        // make assertions
        $this->assertInstanceOf(CustomerProfileExType::class, $class);
    }

    public function testGetUpdateCustomerProfileRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getUpdateCustomerProfileRequest();

        // make assertions
        $this->assertInstanceOf(UpdateCustomerProfileRequest::class, $class);
    }

    public function testGetUpdateCustomerProfileControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(UpdateCustomerProfileRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getUpdateCustomerProfileController($requestMock);

        // make assertions
        $this->assertInstanceOf(UpdateCustomerProfileController::class, $class);
    }

    public function testGetDeleteCustomerProfileRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getDeleteCustomerProfileRequest();

        // make assertions
        $this->assertInstanceOf(DeleteCustomerProfileRequest::class, $class);
    }

    public function testGetDeleteCustomerProfileControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(DeleteCustomerProfileRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getDeleteCustomerProfileController($requestMock);

        // make assertions
        $this->assertInstanceOf(DeleteCustomerProfileController::class, $class);
    }

    public function testGetCreateCustomerPaymentProfileRequestIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCreateCustomerPaymentProfileRequest();

        // make assertions
        $this->assertInstanceOf(CreateCustomerPaymentProfileRequest::class, $class);
    }

    public function testGetCreateCustomerPaymentProfileControllerIsSuccess()
    {
        // set up fixtures
        $authMock = $this->createMock(MerchantAuthenticationType::class);
        $requestMock = $this->createMock(CreateCustomerPaymentProfileRequest::class);
        $requestMock->method('getMerchantAuthentication')
            ->willReturn($authMock);

        // exercise SUT
        $class = $this->factory->getCreateCustomerPaymentProfileController($requestMock);

        // make assertions
        $this->assertInstanceOf(CreateCustomerPaymentProfileController::class, $class);
    }

    public function getCustomerPaymentProfileTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCustomerPaymentProfileType();

        // make assertions
        $this->assertInstanceOf(CustomerPaymentProfileType::class, $class);
    }

    public function testGetCreditCardTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getCreditCardType();

        // make assertions
        $this->assertInstanceOf(CreditCardType::class, $class);
    }

    public function testGetPaymentTypeIsSuccess()
    {
        // exercise SUT
        $class = $this->factory->getPaymentType();

        // make assertions
        $this->assertInstanceOf(PaymentType::class, $class);
    }
}