<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/21/18
 */


namespace Test\Unit\IdentityAccess\Entity\Factory;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Entity\Factory\UserFactory;
use PapaLocal\IdentityAccess\Entity\Person;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class UserFactoryTest.
 *
 * @package Test\Unit\IdentityAccess\Entity\Factory
 */
class UserFactoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $serializerMock;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->serializerMock = $this->createMock(Serializer::class);
        $this->userFactory = new UserFactory($this->serializerMock);
    }

    public function testCanCreateUserFromRecord()
    {
        // set up fixtures
        $userId = 1;
        $userGuid = 'dd064763-e0b3-4792-83e4-310abe065219';
        $username = 'test@papalocal.com';
        $password = '$2y$13$Jv3.kgr033tMjNvXlhqUXODN7T8sVp4f1kMGf0BzaihGBOcGZomG';
        $notificationSavePoint = 15;
        $isActive = true;
        $userTimeCreated = '2018-10-01 12:21:13';
        $personId = 2;
        $personGuid = '567ab5b2-3d2b-451e-a57e-c2a34771a8a4';
        $firstName = 'Guy';
        $lastName = 'Tester';
        $about = 'Something about Guy.';

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('about'))
            ->willReturn(true);
        $recordMock->expects($this->exactly(13))
            ->method('offsetGet')
            ->withConsecutive(
                [$this->equalTo('userId')],
                [$this->equalTo('userGuid')],
                [$this->equalTo('username')],
                [$this->equalTo('password')],
                [$this->equalTo('userTimeCreated')],
                [$this->equalTo('notificationSavePoint')],
                [$this->equalTo('isActive')],
                [$this->equalTo('personId')],
                [$this->equalTo('personGuid')],
                [$this->equalTo('firstName')],
                [$this->equalTo('lastName')],
                [$this->equalTo('about')],
                [$this->equalTo('about')]
            )
            ->willReturnOnConsecutiveCalls(
                $userId,
                $userGuid,
                $username,
                $password,
                $userTimeCreated,
                $notificationSavePoint,
                $isActive,
                $personId,
                $personGuid,
                $firstName,
                $lastName,
                $about,
                $about,
                $about
            );

        $personMock = $this->createMock(Person::class);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('setPerson')
            ->with($personMock)
            ->willReturn($userMock);

        $this->serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->withConsecutive(
                [
                    $this->equalTo(array(
                        'id' => $userId,
                        'guid' => new Guid($userGuid),
                        'username' => $username,
                        'password' => $password,
                        'timeCreated' => $userTimeCreated,
                        'notificationSavePoint' => $notificationSavePoint,
                        'isActive' => $isActive
                    )),
                    $this->equalTo(User::class),
                    $this->equalTo('array')
                ],
                [
                    $this->equalTo(array(
                        'id' => $personId,
                        'guid' => array('value' => $personGuid),
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'about' => $about
                    )),
                    $this->equalTo(Person::class),
                    $this->equalTo('array')
                ]
            )
            ->willReturnOnConsecutiveCalls($userMock, $personMock);

        // exercise SUT
        $result = $this->userFactory->createFromRecord($recordMock);

        // make assertions
        $this->assertEquals($userMock, $result);
    }
}