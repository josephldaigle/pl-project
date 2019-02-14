<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 12:03 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateServices;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\ReferralAgreement\ValueObject\ServiceType;
use PHPUnit\Framework\TestCase;


/**
 * Class SaveAgreementTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateServicesTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 2 provided to)(.)+(cannot be empty)/
     */
    public function testConstructorThrowsExceptionWhenServicesCollectionIsEmpty()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $servicesMock = $this->createMock(IncludeExcludeList::class);
        $servicesMock->expects($this->once())
            ->method('all')
            ->willReturn(array());

        // exercise SUT
        $command = new UpdateServices($guidMock, $servicesMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(All elements provided in param 2 to)(.)+(must be instances of)/
     */
    public function testConstructorThrowsExceptionWhenUnderlyingElementsNotServiceInstances()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $arr = array(array());

        $servicesMock = $this->createMock(IncludeExcludeList::class);
        $servicesMock->expects($this->once())
                     ->method('all')
                     ->willReturn($arr);

        // exercise SUT
        $command = new UpdateServices($guidMock, $servicesMock);
    }

    public function testCanInstantiate()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $svcTypeMock = $this->createMock(ServiceType::class);
        $svcTypeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('include');

        $serviceMock = $this->createMock(Service::class);
        $serviceMock->expects($this->once())
            ->method('getService')
            ->willReturn('Some service');
        $serviceMock->expects($this->once())
            ->method('getType')
            ->willReturn($svcTypeMock);

        $servicesMock = $this->createMock(IncludeExcludeList::class);
        $servicesMock->expects($this->once())
            ->method('all')
            ->willReturn([$serviceMock]);

        // exercise SUT
        $command = new UpdateServices($guidMock, $servicesMock);

        // make assertions
        $this->assertInstanceOf(UpdateServices::class, $command, 'unexpected type');

        $this->assertTrue(is_array($command->getServices()), 'unexpected return value getServices()');
    }
}