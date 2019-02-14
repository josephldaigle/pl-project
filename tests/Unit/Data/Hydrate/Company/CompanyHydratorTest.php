<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/7/18
 * Time: 9:07 PM
 */

namespace Test\Unit\Data\Hydrate\Company;


use PapaLocal\Data\Hydrate\Company\CompanyContactProfileHydrator;
use PapaLocal\Data\Hydrate\Company\CompanyHydrator;
use Symfony\Component\Serializer\Serializer;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\EntityFactory;
use PHPUnit\Framework\TestCase;


/**
 * CompanyHydratorTest.
 *
 * @package Test\Unit\Data\Hydrate\Company
 */
class CompanyHydratorTest extends TestCase
{
    public function testCanInstantiate()
    {
        // set up fixtures
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $entityFacMock = $this->createMock(EntityFactory::class);
        $serializerMock = $this->createMock(Serializer::class);
        $profileHydratorMock = $this->createMock(CompanyContactProfileHydrator::class);

        // exercise SUT
        $hydrator = new CompanyHydrator($tableGatewayMock, $entityFacMock, $serializerMock, $profileHydratorMock);
        
        // make assertions
        $this->assertInstanceOf(CompanyHydrator::class, $hydrator, 'unexpected type');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(Entity supplied must have an id assigned)/
     */
    public function testHydrateThrowsExceptionWhenIdNotSupplied()
    {
        // set up fixtures
        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $tableGatewayMock = $this->createMock(TableGateway::class);

        $entityFacMock = $this->createMock(EntityFactory::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('normalize')
            ->with($companyMock);

        $profileHydratorMock = $this->createMock(CompanyContactProfileHydrator::class);

        // exercise SUT
        $hydrator = new CompanyHydrator($tableGatewayMock, $entityFacMock, $serializerMock, $profileHydratorMock);
        $hydrator->setEntity($companyMock);
        $hydrator->hydrate();

    }

    /**
     * @expectedException PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @expectedExceptionMessageRegExp /^(Unable to find a matching company)/
     */
    public function testHydrateThrowsExceptionWhenCompanyNotFound()
    {
        // set up fixtures
        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(0);

        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company');
        $tableGatewayMock->expects($this->once())
            ->method('findById')
            ->with(0);
        $entityFacMock = $this->createMock(EntityFactory::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('normalize')
            ->with($companyMock);

        $profileHydratorMock = $this->createMock(CompanyContactProfileHydrator::class);

        // exercise SUT
        $hydrator = new CompanyHydrator($tableGatewayMock, $entityFacMock, $serializerMock, $profileHydratorMock);
        $hydrator->setEntity($companyMock);
        $hydrator->hydrate();
    }

    public function testHydrateReturnsHydratedCompanyOnSuccess()
    {
        // set up fixtures
        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(0);

        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company');
        $tableGatewayMock->expects($this->once())
            ->method('findById')
            ->with(0)
            ->willReturn(array(array('name' => 'Test Company')));
        $entityFacMock = $this->createMock(EntityFactory::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('denormalize')
            ->willReturn((new Company())
                ->setName('Test Company'));

        $profileHydratorMock = $this->createMock(CompanyContactProfileHydrator::class);

        // exercise SUT
        $hydrator = new CompanyHydrator($tableGatewayMock, $entityFacMock, $serializerMock, $profileHydratorMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydrate();

        // make assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame($result->getName(), 'Test Company', 'unexpected return object');
    }
}