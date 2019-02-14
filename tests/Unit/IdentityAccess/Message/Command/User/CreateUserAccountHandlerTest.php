<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 12:46 PM
 */

namespace Test\Unit\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\IdentityAccess\Entity\UserAccount;
use PapaLocal\IdentityAccess\Message\Command\User\CreateUserAccount;
use PapaLocal\IdentityAccess\Message\Command\User\CreateUserAccountHandler;
use PapaLocal\IdentityAccess\Service\UserService;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class CreateUserAccountHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Message\Command\User
 */
class CreateUserAccountHandlerTest extends TestCase
{

    public function testHandlerIsSuccess()
    {
        $this->markTestIncomplete();
        // set up fixtures
        $username            = 'test@papalocal.com';
        $userGuid            = '32c4dc41-8800-45c9-8efa-257726de6c0e';
        $personGuid          = '002b8967-32ef-415b-b3f5-cb7e0e6975f8';
        $companyGuid         = '81b839e2-074c-414c-a000-826f1518f8a6';
        $password            = 'SomeTestP@$$11';
        $firstName           = 'Guy';
        $lastName            = 'Tester';
        $companyName         = 'Test Company Name';
        $companyEmailAddress = 'testCompany@papalocal.com';
        $companyPhoneNumber  = '77025027744';
        $addressArr          = [
            'streetAddress' => '101 Depot Ct',
            'city'          => 'Peachtree City',
            'state'         => 'GA',
            'zipCode'       => 30369,
        ];

        $personMock = $this->createMock(Person::class);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('setPerson')
                 ->with($this->equalTo($personMock))
                 ->willReturn($userMock);
        $userMock->expects($this->once())
                 ->method('setRoles')
                 ->with($this->equalTo(array(SecurityRole::ROLE_USER())))
                 ->willReturn($userMock);

        $addressMock = $this->createMock(Address::class);
        $companyMock = $this->createMock(Company::class);

        $userAccountMock = $this->createMock(UserAccount::class);

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->expects($this->once())
                        ->method('createUserAccount')
                        ->with($this->equalTo($userAccountMock));

        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->once())
                 ->method('value')
                 ->willReturnOnConsecutiveCalls($personGuid);

        $guidGeneratorMock = $this->createMock(GuidGeneratorInterface::class);
        $guidGeneratorMock->expects($this->exactly(3))
                          ->method('generate')
                          ->willReturn($guidMock);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(5))
                       ->method('denormalize')
                       ->withConsecutive(
                           [
                               $this->equalTo([
                                   'guid'                  => $guidMock,
                                   'username'              => $username,
                                   'password'              => $password,
                                   'notificationSavePoint' => 0,
                                   'isActive'              => true,
                               ]),
                               $this->equalTo(User::class),
                               $this->equalTo('array'),
                           ],
                           [
                               $this->equalTo([
                                   'guid'      => array('value' => $personGuid),
                                   'firstName' => $firstName,
                                   'lastName'  => $lastName,
                               ]),
                               $this->equalTo(Person::class),
                               $this->equalTo('array'),
                           ],
                           [
                               $this->equalTo($addressArr),
                               $this->equalTo(Address::class),
                               $this->equalTo('array'),
                           ],
                           [
                               $this->equalTo([
                                   'guid'         => $guidMock,
                                   'name'         => $companyName,
                                   'emailAddress' => $companyEmailAddress,
                                   'phoneNumber'  => $companyPhoneNumber,
                                   'address'      => $addressMock,
                               ]),
                               $this->equalTo(Company::class),
                               $this->equalTo('array'),
                           ],
                           [
                               $this->equalTo([
                                   'user'    => $userMock,
                                   'person'  => $personMock,
                                   'company' => $companyMock,
                               ]),
                               $this->equalTo(UserAccount::class),
                               $this->equalTo('array'),
                           ])
                       ->willReturnOnConsecutiveCalls($userMock, $personMock, $addressMock, $companyMock,
                           $userAccountMock);

        $commandMock = $this->createMock(CreateUserAccount::class);
        $commandMock->expects($this->once())
                    ->method('getUsername')
                    ->willReturn($username);
        $commandMock->expects($this->once())
                    ->method('getPassword')
                    ->willReturn($password);
        $commandMock->expects($this->once())
                    ->method('getFirstName')
                    ->willReturn($firstName);
        $commandMock->expects($this->once())
                    ->method('getLastName')
                    ->willReturn($lastName);
        $commandMock->expects($this->exactly(2))
                    ->method('getCompanyName')
                    ->willReturn($companyName);
        $commandMock->expects($this->once())
                    ->method('getCompanyEmailAddress')
                    ->willReturn($companyEmailAddress);
        $commandMock->expects($this->once())
                    ->method('getCompanyPhoneNumber')
                    ->willReturn($companyPhoneNumber);
        $commandMock->expects($this->once())
                    ->method('getCompanyAddress')
                    ->willReturn($addressArr);


        // exercise SUT
        $handler = new CreateUserAccountHandler($userServiceMock, $serializerMock, $guidGeneratorMock, $voFactoryMock);
        $handler->__invoke($commandMock);
    }
}