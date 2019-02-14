<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/31/18
 * Time: 9:43 AM
 */

namespace Test\Integration\Referral\Database;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Core\Exception\InvalidStateException;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\Entity\Factory\ReferralFactory;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Serializer\Serializer;


/**
 * Class DataLayerTest
 * @package Test\Integration\Referral\Data
 */
class DataLayerTest extends WebDatabaseTestCase
{
    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var ReferralFactory
     */
    private $referralFactory;

    /**
     * @var MessageBus
     */
    private $mysqlBus;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([
            'Referral',
            'ReferralAgreement'
        ]);

        parent::setUp();

        $this->messageFactory = $this->diContainer->get('PapaLocal\Referral\Data\MessageFactory');
        $this->mysqlBus = $this->diContainer->get('messenger.bus.mysql');
        $this->referralRepository = $this->diContainer->get('PapaLocal\Referral\Data\ReferralRepository');
        $this->tableGateway = $this->diContainer->get('papalocal_data.adapted_table_gateway');
        $this->serializer = $this->diContainer->get('serializer');
    }

    public function testCanSaveReferralWithAgreementRecipientSuccessfully()
    {
        // Fixtures

        $begRowCount = $this->getConnection()->getRowCount('Referral');

        $referral = (new Referral())
            ->setGuid(new Guid('d036440f-786g-j65b-8e2l-76bv9375'))
            ->setProviderUserGuid(new Guid('s976xcud-24jn-sv97-l7s6-2knh4u56'))
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setPhoneNumber(new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()))
            ->setEmailAddress(new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()))
            ->setAddress(new Address('3289 saville', 'atlanta', 'GA', '30301', 'USA'))
            ->setAbout('About referral')
            ->setNote('')
            ->setRecipient(new AgreementRecipient(new Guid('f148440f-789c-4f5d-8e1f-76bc6688')));

        $saveReferralCmd = $this->messageFactory->newSaveReferral($referral, 'created');
        $this->mysqlBus->dispatch($saveReferralCmd);

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM Referral WHERE guid='". $referral->getGuid()->value() ."'")
            ->getRow(0);

        $this->assertTableRowCount('Referral', $begRowCount + 1);
        $this->assertSame($referral->getGuid()->value(), $statusRow['guid']);
        $this->assertSame($referral->getProviderUserGuid()->value(), $statusRow['providerUserGuid']);
        $this->assertSame('created', $statusRow['currentPlace']);
        $this->assertSame($referral->getFirstName(), $statusRow['firstName']);
        $this->assertSame($referral->getLastName(), $statusRow['lastName']);
        $this->assertSame($referral->getPhoneNumber()->getPhoneNumber(), $statusRow['phoneNumber']);
        $this->assertSame($referral->getEmailAddress()->getEmailAddress(), $statusRow['emailAddress']);
        $this->assertEquals($referral->getAddress(), $this->serializer->deserialize($statusRow['address'], Address::class, 'json'));
        $this->assertSame($referral->getAbout(), $statusRow['about']);
        $this->assertSame($referral->getNote(), $statusRow['note']);
    }

    public function testCanSaveReferralWithContactRecipientSuccessfully()
    {
        // Fixtures

        $begRowCount = $this->getConnection()->getRowCount('Referral');

        $referral = (new Referral())
            ->setGuid(new Guid('d036440f-786g-j65b-8e2l-76bv9375'))
            ->setProviderUserGuid(new Guid('s976xcud-24jn-sv97-l7s6-2knh4u56'))
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setPhoneNumber(new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()))
            ->setEmailAddress(new EmailAddress('gtester@papalocal.com', EmailAddressType::PERSONAL()))
            ->setAddress(new Address('2423 rock', 'atlanta', 'GA', '30333', 'USA'))
            ->setAbout('About referral')
            ->setNote('')
            ->setRecipient(new ContactRecipient(
                'Danny',
                'Porch',
                new PhoneNumber('4047666889', PhoneNumberType::PERSONAL()),
                new EmailAddress('dporch@papalocal.com', EmailAddressType::PERSONAL()),
                null
            ));

        $saveReferralCmd = $this->messageFactory->newSaveReferral($referral, 'created');
        $this->mysqlBus->dispatch($saveReferralCmd);

        $statusRow = $this->getConnection()
                ->createQueryTable('status', "SELECT * FROM Referral WHERE guid='". $referral->getGuid()->value() ."'")
                ->getRow(0);

        $this->assertTableRowCount('Referral', $begRowCount + 1);
        $this->assertSame($referral->getGuid()->value(), $statusRow['guid']);
        $this->assertSame($referral->getProviderUserGuid()->value(), $statusRow['providerUserGuid']);
        $this->assertSame('created', $statusRow['currentPlace']);
        $this->assertSame($referral->getFirstName(), $statusRow['firstName']);
        $this->assertSame($referral->getLastName(), $statusRow['lastName']);
        $this->assertSame($referral->getPhoneNumber()->getPhoneNumber(), $statusRow['phoneNumber']);
        $this->assertSame($referral->getEmailAddress()->getEmailAddress(), $statusRow['emailAddress']);
        $this->assertEquals($referral->getAddress(), $this->serializer->deserialize($statusRow['address'], Address::class, 'json'));
        $this->assertSame($referral->getAbout(), $statusRow['about']);
        $this->assertSame($referral->getNote(), $statusRow['note']);
    }

    public function testCanUpdateReferralWithContactRecipientSuccessfully()
    {
        $referral = (new Referral())
            ->setId(1)
            ->setGuid(new Guid('80b6861f-e9d2-457e-a150-d32e8b5632ce'))
            ->setProviderUserGuid(new Guid('0012be8b-7178-42ab-a5d3-b883430d2710'))
            ->setCurrentPlace('created')
            ->setFirstName('Elle')
            ->setLastName('Phillips')
            ->setPhoneNumber(new PhoneNumber('2147483647', PhoneNumberType::PERSONAL()))
            ->setEmailAddress(new EmailAddress('ephillips@papalocal.com', EmailAddressType::PERSONAL()))
            ->setAddress(new Address('2342 Lee Highway', 'Arlington', 'Virginia', '22201', 'United States'))
            ->setAbout('test')
            ->setNote('Note')
            ->setRecipient(new ContactRecipient(
                'John',
                'Doe',
                new PhoneNumber('1234567890', PhoneNumberType::PERSONAL()),
                new EmailAddress('jdoe@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('9464hk5l-9573-95hj-n5k3-f086430d9648')
            ));

        $UpdateReferralCmd = $this->messageFactory->newUpdateReferral($referral, 'acquired');
        $this->mysqlBus->dispatch($UpdateReferralCmd);

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM Referral WHERE guid='". $referral->getGuid()->value() ."'")
            ->getRow(0);

        $this->assertSame($referral->getGuid()->value(), $statusRow['guid']);
        $this->assertSame($referral->getProviderUserGuid()->value(), $statusRow['providerUserGuid']);
        $this->assertSame('acquired', $statusRow['currentPlace']);
        $this->assertSame($referral->getFirstName(), $statusRow['firstName']);
        $this->assertSame($referral->getLastName(), $statusRow['lastName']);
        $this->assertSame($referral->getPhoneNumber()->getPhoneNumber(), $statusRow['phoneNumber']);
        $this->assertSame($referral->getEmailAddress()->getEmailAddress(), $statusRow['emailAddress']);
        $this->assertEquals($referral->getAddress(), $this->serializer->deserialize($statusRow['address'], Address::class, 'json'));
        $this->assertSame($referral->getAbout(), $statusRow['about']);
        $this->assertSame($referral->getNote(), $statusRow['note']);
    }

    public function testCanUpdateReferralWithAgreementRecipientSuccessfully()
    {
        $referral = (new Referral())
            ->setId(10)
            ->setGuid(new Guid('cb848565-7438-42d1-bef7-264905f680a5'))
            ->setProviderUserGuid(new Guid('de80a7c4-7166-43a8-8f18-7f6be924bb90'))
            ->setCurrentPlace('acquired')
            ->setFirstName('Elle')
            ->setLastName('Phillips')
            ->setPhoneNumber(new PhoneNumber('2147483647', PhoneNumberType::PERSONAL()))
            ->setEmailAddress(new EmailAddress('ephillips@papalocal.com', EmailAddressType::PERSONAL()))
            ->setAddress(new Address('2342 Lee Highway', 'Arlington', 'Virginia', '22201', 'United States'))
            ->setAbout('test')
            ->setNote('Note')
            ->setRecipient(new AgreementRecipient(new Guid('de546e18-d8e4-47ea-9294-6da584ed1f49')));


        $UpdateReferralCmd = $this->messageFactory->newUpdateReferral($referral, 'finalized');
        $this->mysqlBus->dispatch($UpdateReferralCmd);

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM Referral WHERE guid='". $referral->getGuid()->value() ."'")
            ->getRow(0);

        $this->assertSame($referral->getGuid()->value(), $statusRow['guid']);
        $this->assertSame($referral->getProviderUserGuid()->value(), $statusRow['providerUserGuid']);
        $this->assertSame('finalized', $statusRow['currentPlace']);
        $this->assertSame($referral->getFirstName(), $statusRow['firstName']);
        $this->assertSame($referral->getLastName(), $statusRow['lastName']);
        $this->assertSame($referral->getPhoneNumber()->getPhoneNumber(), $statusRow['phoneNumber']);
        $this->assertSame($referral->getEmailAddress()->getEmailAddress(), $statusRow['emailAddress']);
        $this->assertEquals($referral->getAddress(), $this->serializer->deserialize($statusRow['address'], Address::class, 'json'));
        $this->assertSame($referral->getAbout(), $statusRow['about']);
        $this->assertSame($referral->getNote(), $statusRow['note']);
    }

    public function testCanFindReferralByGuid()
    {
        // set up fixtures
        $referralGuid = $this->getConnection()
            ->createQueryTable('referral_guid', 'SELECT guid FROM Referral LIMIT 1')
            ->getRow(0)['guid'];

        $guid = new Guid($referralGuid);

        // exercise SUT
        $referral = $this->referralRepository->fetchByGuid($guid);

        // make assertions
        $this->assertInstanceOf(Referral::class, $referral);
    }

    public function testCanFindReferralByRecipientEmailAddress()
    {
        // set up fixtures
        $emailAddress = $this->getConnection()
            ->createQueryTable('email', 'SELECT recipientEmailAddress FROM Referral LIMIT 1')
            ->getRow(0)['recipientEmailAddress'];
        $expectedRowCount = intval($this->getConnection()
            ->createQueryTable('exp_row_count', 'SELECT COUNT(recipientEmailAddress) as \'count\' FROM Referral WHERE recipientEmailAddress = \'' . $emailAddress . '\'')
            ->getRow(0)['count']);

        $emailAddress = new EmailAddress($emailAddress, EmailAddressType::PERSONAL());

        // exercise SUT
        $referralList = $this->referralRepository->fetchByRecipientEmailAddress($emailAddress);

        // make assertions
        $this->assertInstanceOf(Collection::class, $referralList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $referralList->count(), 'unexpected result count');
    }

    public function testCanFindReferralByAgreementOwnerGuid()
    {
        // set up fixtures
        $agreementOwnerGuid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT agreementOwnerGuid FROM v_referral WHERE agreementOwnerGuid IS NOT NULL LIMIT 1')
            ->getRow(0)['agreementOwnerGuid'];
        $expectedRowCount = intval($this->getConnection()
            ->createQueryTable('exp_row_count', 'SELECT COUNT(agreementOwnerGuid) as \'count\' FROM v_referral WHERE agreementOwnerGuid = \'' . $agreementOwnerGuid . '\'')
            ->getRow(0)['count']);

        $guid = new Guid($agreementOwnerGuid);

        // exercise SUT
        $referralList = $this->referralRepository->fetchByAgreementOwnerGuid($guid);

        // make assertions
        $this->assertInstanceOf(Collection::class, $referralList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $referralList->count(), 'unexpected result count');
    }

    public function testCanFindReferralByContactGuid()
    {
        // set up fixtures
        $contactGuid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT contactGuid FROM v_referral WHERE contactGuid IS NOT NULL LIMIT 1')
            ->getRow(0)['contactGuid'];
        $expectedRowCount = intval($this->getConnection()
            ->createQueryTable('exp_row_count', 'SELECT COUNT(contactGuid) as \'count\' FROM v_referral WHERE contactGuid = \'' . $contactGuid . '\'')
            ->getRow(0)['count']);

        $guid = new Guid($contactGuid);

        // exercise SUT
        $referralList = $this->referralRepository->fetchByContactGuid($guid);

        // make assertions
        $this->assertInstanceOf(Collection::class, $referralList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $referralList->count(), 'unexpected result count');
    }

    public function testCanFindReferralByProviderGuid()
    {
        // set up fixtures
        $providerGuid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT providerUserGuid FROM v_referral LIMIT 1')
            ->getRow(0)['providerUserGuid'];
        $expectedRowCount = intval($this->getConnection()
            ->createQueryTable('exp_row_count', 'SELECT COUNT(providerUserGuid) as \'count\' FROM v_referral WHERE providerUserGuid = \'' . $providerGuid . '\'')
            ->getRow(0)['count']);

        $guid = new Guid($providerGuid);

        // exercise SUT
        $referralList = $this->referralRepository->fetchByProviderGuid($guid);

        // make assertions
        $this->assertInstanceOf(Collection::class, $referralList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $referralList->count(), 'unexpected result count');


        $guid = '0012be8b-7178-42ab-a5d3-b883430d2710';
        $referralList = $this->referralRepository->fetchByProviderGuid(new Guid($guid));
    }
}