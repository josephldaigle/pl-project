<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 11:23 PM
 */


namespace Test\Integration\Core\Security;


use PapaLocal\Core\Security\Cryptographer;
use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\Core\Security\SecureLinkGenerator;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SecureLinkGeneratorTest
 *
 * @package Test\Integration\Core\Security
 */
class SecureLinkGeneratorTest extends KernelTestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();

        // fetch serializer from container
        $this->serializer = static::$kernel->getContainer()->get('serializer');
    }

    public function testGenerateSecureLinkCanSerializeEmailSalt()
    {
        // set up fixtures
        $guidMock = $this->createMock(Guid::class);
        $guidMock->expects($this->exactly(1))
             ->method('value')
             ->willReturn('486c7e98-6088-4bde-b5d2-86b2b469926c');

        $guidGeneratorMock = $this->createMock(GuidGeneratorInterface::class);
        $guidGeneratorMock->method('generate')
            ->willReturn($guidMock);

        $cryptographerMock = $this->createMock(Cryptographer::class);
        $cryptographerMock->expects($this->once())
            ->method('createSalt')
            ->willReturn(7113785713296932);
        $cryptographerMock->expects($this->once())
            ->method('createHash')
            ->willReturn('$2y$13$DLelY/9DgSEgpKhpKqMDe.1AN049iJrBZso7Fb/yut71kQVVMP2.2');

        $saltRepositoryMock = $this->createMock(EmailSaltRepository::class);
        $saltRepositoryMock->expects($this->once())
            ->method('save');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($this->once())
            ->method('generate')
            ->willReturn('test complete');

        $emailMock = $this->createMock(EmailAddress::class);
        $emailMock->expects($this->exactly(3))
            ->method('getEmailAddress')
            ->willReturn('test@papalocal.com');

        $purposeMock = $this->createMock(EmailSaltPurpose::class);
        $purposeMock->expects($this->exactly(3))
            ->method('getValue')
            ->willReturn(EmailSaltPurpose::PURPOSE_FORGOT_PASS()->getValue());

        $interval = new \DateInterval('PT30M');

        // exercise SUT
        $secureLinkGenerator = new SecureLinkGenerator($guidGeneratorMock, $saltRepositoryMock, $this->serializer, $routerMock, $cryptographerMock);
        $url = $secureLinkGenerator->generateSecureLink($emailMock, $purposeMock, '', $interval, array());

        // make assertions
        $this->assertSame('test complete', $url);
    }

}