<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 8:40 AM
 */

namespace Test\Unit\ReferralAgreement\Data;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\SaveAgreement;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementName;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementStatus;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateLocations;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateQuantity;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateReferralPrice;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateServices;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateStrategy;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\AcceptInvitation;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\AssignUserGuid;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\DeclineInvitation;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\MarkInvitationSent;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\RemoveInvitee;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInvitee;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PHPUnit\Framework\TestCase;


/**
 * Class MessageFactoryTest
 *
 * @package Test\Unit\ReferralAgreement\Data
 */
class MessageFactoryTest extends TestCase
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->messageFactory = new MessageFactory();
    }

    public function testCanCreateNewSaveAgreement()
    {
        $refAgmtMock = $this->createMock(ReferralAgreement::class);

        $result = $this->messageFactory->newSaveAgreement($refAgmtMock);

        $this->assertInstanceOf(SaveAgreement::class, $result, 'unexpected return type');
    }

    public function testCanCreateUpdateLocations()
    {
        // set up fixtures
        $agmtGuidMock = $this->createMock(GuidInterface::class);


        $locMock1 = $this->createMock(Location::class);
        $locMock2 = $this->createMock(Location::class);

        $locationListMock = $this->createMock(IncludeExcludeList::class);
        $locationListMock->expects($this->once())
                         ->method('all')
                         ->willReturn(array($locMock1, $locMock2));

        // exercise SUT
        $message = $this->messageFactory->newUpdateLocations($agmtGuidMock, $locationListMock);

        // make assertions
        $this->assertInstanceOf(UpdateLocations::class, $message, 'unexpected type');
    }

    public function testCanCreateUpdateServices()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->any())
                 ->method('value')
                 ->willReturn('e035d7b2-cf9c-4bb0-a119-a57de59edbfe');

        $servicesMock = $this->createMock(IncludeExcludeList::class);

        $serviceMock = $this->createMock(Service::class);

        $servicesMock->expects($this->once())
                     ->method('all')
                     ->willReturn([$serviceMock]);

        // exercise SUT
        $result = $this->messageFactory->newUpdateServices($guidMock, $servicesMock);

        // make assertions
        $this->assertInstanceOf(UpdateServices::class, $result);
    }

    public function testCanCreateSaveInvitee()
    {
        // set up fixtures
        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);

        // exercise SUT
        $command = new SaveInvitee($inviteeMock);

        // make assertions
        $this->assertInstanceOf(SaveInvitee::class, $command);
    }

    public function testCanCreateNewUpdateAgreementStatus()
    {
        $statusMock = $this->createMock(AgreementStatus::class);

        $result = $this->messageFactory->newUpdateAgreementStatus($statusMock);

        $this->assertInstanceOf(UpdateAgreementStatus::class, $result, 'unexpected return type');
    }

    public function testCanCreateUpdateAgreementName()
    {
        //set up fixtures
        $agmtGuidMock = $this->createMock(GuidInterface::class);
        $agmtName     = 'New Test Agreement Name';

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementName($agmtGuidMock, $agmtName);

        // make assertions
        $this->assertInstanceOf(UpdateAgreementName::class, $command, 'unexpected type');
    }

    public function testCanCreateUpdateAgreementDescription()
    {
        // set up fixtures
        $agmtGuidMock    = $this->createMock(GuidInterface::class);
        $agmtDescription = 'New test agreement description.';

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementDescription($agmtGuidMock, $agmtDescription);

        // make assertions
        $this->assertInstanceOf(UpdateDescription::class, $command);
    }

    public function testCanCreateUpdateAgreementQuantity()
    {
        // set up fixtures
        $agmtGuidMock    = $this->createMock(GuidInterface::class);
        $quantity = 10;

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementQuantity($agmtGuidMock, $quantity);

        // make assertions
        $this->assertInstanceOf(UpdateQuantity::class, $command);
    }

    public function testCanCreateUpdateAgreementStrategy()
    {
        // set up fixtures
        $agmtGuidMock    = $this->createMock(GuidInterface::class);
        $strategy = 'weekly';

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementStrategy($agmtGuidMock, $strategy);

        // make assertions
        $this->assertInstanceOf(UpdateStrategy::class, $command);
    }

    public function testCanCreateUpdateReferralPrice()
    {
        // set up fixtures
        $agmtGuidMock    = $this->createMock(GuidInterface::class);
        $price = 35.00;

        // exercise SUT
        $command = $this->messageFactory->newUpdateReferralPrice($agmtGuidMock, $price);

        // make assertions
        $this->assertInstanceOf(UpdateReferralPrice::class, $command);
    }


    /**************
     * Invitee
     *************/
    public function testCanCreateNewSaveInvitee()
    {
        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);

        // exercise SUT
        $command = $this->messageFactory->newSaveInvitee($inviteeMock);

        // make assertions
        $this->assertInstanceOf(SaveInvitee::class, $command);
    }

    public function testCanCreateNewAcceptInvitation()
    {
        $agreementGuid = 'b74aa5ee-cfe2-41af-a4b6-ecffe63141ba';
        $emailAddress = 'test@papalocal.com';

        // exercise SUT
        $command = $this->messageFactory->newAcceptInvitation($agreementGuid, $emailAddress);

        // make assertions
        $this->assertInstanceOf(AcceptInvitation::class, $command);
    }

    public function testCanCreateNewDeclineInvitation()
    {
        $inviteeGuidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $command = $this->messageFactory->newDeclineInvitation($inviteeGuidMock);

        // make assertions
        $this->assertInstanceOf(DeclineInvitation::class, $command);
    }

    public function testCanCreateMarkInvitationSent()
    {
        $inviteeGuidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $command = $this->messageFactory->newMarkInvitationSent($inviteeGuidMock);

        // make assertions
        $this->assertInstanceOf(MarkInvitationSent::class, $command);
    }

    public function testCanCreateNewAssignUserGuidToInvitee()
    {
        $emailAddress = 'test@papalocal.com';
        $userGuid = 'b74aa5ee-cfe2-41af-a4b6-ecffe63141ba';

        // exercise SUT
        $command = $this->messageFactory->newAssignUserGuidToInvitee($emailAddress, $userGuid);

        // make assertions
        $this->assertInstanceOf(AssignUserGuid::class, $command);
    }

    public function testCanCreateNewRemoveInvitee()
    {
        $inviteeGuid = $this->createMock(GuidInterface::class);

        // exercise SUT
        $command = $this->messageFactory->newRemoveInvitee($inviteeGuid);

        // make assertions
        $this->assertInstanceOf(RemoveInvitee::class, $command);
    }
}