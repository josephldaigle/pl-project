<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/9/18
 */


namespace Test\Unit\Core\Security;


use PapaLocal\Entity\Exception\QueryException;
use PapaLocal\Core\Security\ValueObject\EmailSalt;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Test\DataResourcesMockingTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class EmailSaltRepositoryTest
 *
 * @package Test\Unit\Core\Security
 */
class EmailSaltRepositoryTest extends TestCase
{

    use DataResourcesMockingTrait;

    /**
     * @var TableGateway
     */
    private $tableGatewayMock;

    /**
     * @var Serializer
     */
    private $serializerMock;

    /**
     * @var Mapper
     */
    private $mapperMock;


    /**
     * @var CommandFactory
     */
    private $commandFactoryMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tableGatewayMock = $this->createMock(TableGateway::class);
        $this->serializerMock = $this->createMock(Serializer::class);
        $this->mapperMock = $this->createMock(Mapper::class);
        $this->commandFactoryMock = $this->createMock(CommandFactory::class);
    }

    /**
     * @expectedException \PapaLocal\Entity\Exception\QueryException
     * @expectedExceptionMessageRegExp /^(Unable to create email key)(.)+(test control message)/
     */
    public function testSaveThrowsExceptionWhenSaltExists()
    {
        // set up fixtures
        $emailSaltMock = $this->createMock(EmailSalt::class);

        $this->tableGatewayMock->expects($this->once())
            ->method('create')
            ->will($this->throwException(new QueryException('test control message')));

        $dataPool = $this->getDataResourcePoolMock($this->tableGatewayMock, $this->serializerMock, $this->mapperMock, $this->commandFactoryMock);

        $repositoryMock = $this->getMockBuilder(EmailSaltRepository::class)
            ->setConstructorArgs([$dataPool])
            ->setMethodsExcept(['save'])
            ->getMock();

        // exercise SUT
        $repositoryMock->save($emailSaltMock);
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\QueryException
     * @expectedExceptionMessageRegExp /^(Unable to create email key)(.)+(test control message)/
     */
    public function testSaveThrowsExceptionWhenCreateFails()
    {
        // set up fixtures
        $emailSaltMock = $this->createMock(EmailSalt::class);

        $this->tableGatewayMock->expects($this->once())
            ->method('create')
            ->will($this->throwException(new QueryException('test control message')));

        $dataPool = $this->getDataResourcePoolMock($this->tableGatewayMock, $this->serializerMock, $this->mapperMock, $this->commandFactoryMock);

        $repository = new EmailSaltRepository($dataPool);

        // exercise SUT
        $repository->save($emailSaltMock);
    }
}