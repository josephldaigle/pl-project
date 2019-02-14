<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/2/18
 */

namespace Test\Integration\ReferralAgreement\Entity;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\Factory\InviteeFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class InviteeFactoryTest.
 *
 * @package Test\Integration\ReferralAgreement\Entity
 */
class InviteeFactoryTest extends KernelTestCase
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
        // boot kernel
        self::bootKernel();

        $this->serializer = self::$kernel->getContainer()->get('serializer');
    }

    public function testCanCreateFromRecord()
    {
        $row = array(
            'guid' => '22cc89b4-3321-4aed-ba43-89fd2b3d914f',
            'agreementGuid' => '264059a7-b8b6-48a4-afcb-0133a87ea4f9',
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'message' => 'Guy, sell me referrals.',
            'emailAddress' => 'test@example.com',
            'phoneNumber' => 8881114444,
            'userGuid' => '8cb59c41-1b5c-48a7-a2b2-401183a75e0e',
            'timeSent' => '2018-10-11 11:21:14',
            'declined' => 0,
            'isParticipant' => 0
        );

        $record = new Record($row);

        $factory = new InviteeFactory($this->serializer);

        $invitee = $factory->createFromRecord($record);

        $this->assertInstanceOf(ReferralAgreementInvitee::class, $invitee);
        $this->assertEquals($row['userGuid'], $invitee->getUserId()->value(), 'unexpected userGuid');
        $this->assertTrue($invitee->isUser(), 'unexpected isUser');
        $this->assertEquals($row['emailAddress'], $invitee->getEmailAddress()->getEmailAddress(), 'unexpected email address');
        $this->assertEquals($row['firstName'], $invitee->getFirstName(), 'unexpected first name');
        $this->assertEquals($row['lastName'], $invitee->getLastName(), 'unexpected last name');
        $this->assertEquals($row['message'], $invitee->getMessage(), 'unexpected message');
        $this->assertEquals($row['phoneNumber'], $invitee->getPhoneNumber()->getPhoneNumber(), 'unexpected phone number');
        $this->assertEquals($row['timeSent'], $invitee->getTimeNotified(), 'unexpected time notified');
        $this->assertFalse($invitee->isDeclined(), 'unexpected isDeclined');
        $this->assertFalse($invitee->isParticipant(), 'unexpected isParticipant');
        $this->assertSame('Invited', $invitee->getCurrentPlace(), 'unexpected current place');
    }

    public function testCanCreateFromRecordSet()
    {
        // set up fixtures
        $recordSet = new RecordSet(array(
            new Record(array(
                'guid' => '22cc89b4-3321-4aed-ba43-89fd2b3d914f',
                'agreementGuid' => '264059a7-b8b6-48a4-afcb-0133a87ea4f9',
                'firstName' => 'Guy',
                'lastName' => 'Tester',
                'message' => 'Guy, sell me referrals.',
                'emailAddress' => 'test@example.com',
                'phoneNumber' => 8881114444,
                'userGuid' => '8cb59c41-1b5c-48a7-a2b2-401183a75e0e',
                'timeSent' => '2018-10-11 11:21:14',
                'declined' => 0,
                'isParticipant' => 0
            ))
        ));

        $factory = new InviteeFactory($this->serializer);

        // exercise SUT
        $result = $factory->createFromRecordSet($recordSet);

        // make assertions
        $this->assertInstanceOf(Collection::class, $result, 'unexpected type');
        $this->assertEquals($recordSet->count(), $result->count());
    }
}