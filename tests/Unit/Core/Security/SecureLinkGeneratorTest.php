<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/7/18
 * Time: 7:57 AM
 */


namespace Test\Unit\Core\Security;


use PapaLocal\Core\Security\Cryptographer;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\Core\Security\SecureLinkGenerator;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class SecureLinkGeneratorTest
 *
 * @package Test\Unit\Core\Security
 */
class SecureLinkGeneratorTest extends TestCase
{

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
    }

    public function testGetUrlReplacesUnsecuredProtocol()
    {
        // set up fixtures
        $guidGeneratorMock = $this->createMock(GuidGeneratorInterface::class);

        $saltRepositoryMock = $this->createMock(EmailSaltRepository::class);

        $cryptographerMock = $this->createMock(Cryptographer::class);

        $serializerMock = $this->createMock(SerializerInterface::class);

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($this->once())
            ->method('generate')
            ->willReturn('http://www.papalocal.com/login');

        $linkGenerator = $this->getMockBuilder(SecureLinkGenerator::class)
            ->setConstructorArgs(array($guidGeneratorMock, $saltRepositoryMock, $serializerMock, $routerMock, $cryptographerMock))
            ->setMethodsExcept(['getUrl'])
            ->getMock();

        // exercise SUT
        $result = $linkGenerator->getUrl('login', array(), UrlGeneratorInterface::ABSOLUTE_URL);

        // make assertions
        $this->assertTrue(is_string($result), 'unexpected type');
        $this->assertStringStartsWith('https://', $result, 'unexpected prefix');
    }

}