<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/8/18
 * Time: 12:24 PM
 */

namespace Test\Unit\Data\Hydrate\Company;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Hydrate\Company\CompanyContactProfileHydrator;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\ValueObject\ContactProfile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * CompanyContactProfileHydratorTest.
 *
 * @package Test\Unit\Data\Hydrate\Company
 */
class CompanyContactProfileHydratorTest extends TestCase
{
    public function testCanInstantiate()
    {
        //set up fixtures
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $entityFacMock = $this->createMock(EntityFactory::class);
        $serializerMock = $this->createMock(Serializer::class);
        $profileHydratorMock = $this->createMock(CompanyContactProfileHydrator::class);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock, $profileHydratorMock);

        // make assertions
        $this->assertInstanceOf(CompanyContactProfileHydrator::class, $hydrator, 'unexpected type');
    }

    public function testHydrateAddressListSetsEmptyAddressListWhenNoRecordsFound()
    {
        // set up fixtures

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->never())
            ->method('addAddress');
        $contactProfileMock->expects($this->once())
            ->method('getAddressList')
            ->willReturn($collectionMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_address');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array());

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydrateAddressList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(0, $contactProfile->getAddressList()->count(),
            'unexpected address list count');

    }

    public function testHydrateAddressListReturnsAddressListWhenRecordsFound()
    {
        // set up fixtures

        // configure Address mock
        $addressMock = $this->createMock(Address::class);
        $addressMock->expects($this->once())
            ->method('getId')
            ->willReturn('addressCheck');

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->once())
            ->method('addAddress')
            ->with($addressMock);
        $contactProfileMock->expects($this->once())
            ->method('getAddressList')
            ->willReturn($collectionMock);
        $contactProfileMock->expects($this->once())
            ->method('findAddressBy')
            ->with('type', AttrType::ADDRESS_PHYSICAL)
            ->willReturn($addressMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_address');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array(array('id' => 'addressCheck')));

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(3))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock, $addressMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydrateAddressList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(1, $contactProfile->getAddressList()->count(),
            'unexpected address list count');
        $addressId = $contactProfile->findAddressBy('type', AttrType::ADDRESS_PHYSICAL)->getId();
        $this->assertSame('addressCheck', $addressId, 'unexpected address validation result');

    }

    public function testHydratePhoneNumberListReturnsPhoneListWhenRecordsFound()
    {
        // set up fixtures

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->never())
            ->method('addPhoneNumber');
        $contactProfileMock->expects($this->once())
            ->method('getPhoneNumberList')
            ->willReturn($collectionMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_phone');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array());

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydratePhoneNumberList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(0, $contactProfile->getPhoneNumberList()->count(),
            'unexpected phone list count');
    }

    public function testHydratePhoneNumberListSetsEmptyPhoneNumberListWhenNoRecordFound()
    {
        // set up fixtures

        // configure Address mock
        $phoneNumberMock = $this->createMock(PhoneNumber::class);
        $phoneNumberMock->expects($this->once())
            ->method('getId')
            ->willReturn('phoneNumberCheck');

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->once())
            ->method('addPhoneNumber')
            ->with($phoneNumberMock);
        $contactProfileMock->expects($this->once())
            ->method('getPhoneNumberList')
            ->willReturn($collectionMock);
        $contactProfileMock->expects($this->once())
            ->method('findPhoneNumberBy')
            ->with('type', AttrType::PHONE_MAIN)
            ->willReturn($phoneNumberMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_phone');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array(array('id' => 'phoneNumberCheck')));

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(3))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock, $phoneNumberMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydratePhoneNumberList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(1, $contactProfile->getPhoneNumberList()->count(),
            'unexpected phone list count');
        $phoneId = $contactProfile->findPhoneNumberBy('type', AttrType::PHONE_MAIN)->getId();
        $this->assertSame('phoneNumberCheck', $phoneId, 'unexpected phone validation result');
    }

    public function testHydrateEmailAddressListSetsEmptyEmailAddressListWhenNoRecordsFound()
    {
        // set up fixtures

        // configure Address mock
        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getId')
            ->willReturn('emailAddressCheck');

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(1);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->once())
            ->method('addEmailAddress')
            ->with($emailAddressMock);
        $contactProfileMock->expects($this->once())
            ->method('getEmailAddressList')
            ->willReturn($collectionMock);
        $contactProfileMock->expects($this->once())
            ->method('findEmailAddressBy')
            ->with('type', AttrType::EMAIL_PRIMARY)
            ->willReturn($emailAddressMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_email');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array(array('id' => 'emailAddressCheck')));

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(3))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock, $emailAddressMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydrateEmailAddressList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(1, $contactProfile->getEmailAddressList()->count(),
            'unexpected address list count');
        $emailId = $contactProfile->findEmailAddressBy('type', AttrType::EMAIL_PRIMARY)->getId();
        $this->assertSame('emailAddressCheck', $emailId, 'unexpected address validation result');
    }

    public function testHydrateEmailAddressListSetsEmptyEmailAddressListWhenRecordsFound()
    {
        // set up fixtures

        //configure Collection mock
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        // configure ContactProfile mock
        $contactProfileMock = $this->createMock(ContactProfile::class);
        $contactProfileMock->expects($this->never())
            ->method('addEmailAddress');
        $contactProfileMock->expects($this->once())
            ->method('getEmailAddressList')
            ->willReturn($collectionMock);

        $companyMock = $this->createMock(Company::class);
        $companyMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(99);
        $companyMock->expects($this->once())
            ->method('getContactProfile')
            ->willReturn($contactProfileMock);

        // configure TableGateway mock
        $tableGatewayMock = $this->createMock(TableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with('v_company_email');
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with('companyId', 99)
            ->willReturn(array());

        // configure EntityFactory mock
        $entityFacMock = $this->createMock(EntityFactory::class);

        // configure Serializer mock
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($collectionMock, $contactProfileMock);

        // exercise SUT
        $hydrator = new CompanyContactProfileHydrator($tableGatewayMock, $entityFacMock, $serializerMock);
        $hydrator->setEntity($companyMock);
        $result = $hydrator->hydrateEmailAddressList();

        // make assertions

        // result object assertions
        $this->assertInstanceOf(Company::class, $result, 'unexpected type');
        $this->assertSame(99, $result->getId(), 'unexpected company validation result');

        // contact profile assertions
        $contactProfile = $result->getContactProfile();
        $this->assertSame(0, $contactProfile->getEmailAddressList()->count(),
            'unexpected email list count');
    }
}