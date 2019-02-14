<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/17/18
 * Time: 3:08 PM
 */

namespace Test\Unit\AuthorizeDotNet;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNetFactory;
use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AuthorizeDotNetTest extends TestCase
{
    private $environment;
    private $authentication;
    private $aNetFactory;
    private $logger;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();


        // set up fixtures
        $this->environment = ANetEnvironment::SANDBOX;
        $this->authentication = $this->createMock(MerchantAuthenticationType::class);
        $this->aNetFactory = $this->createMock(AuthorizeDotNetFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }


    public function testCanInstantiate()
    {
        // exercise SUT
        $aNet = new AuthorizeDotNet($this->environment, $this->authentication, $this->aNetFactory, $this->logger);

        // make assertions
        $this->assertInstanceOf(AuthorizeDotNet::class, $aNet);
    }
}