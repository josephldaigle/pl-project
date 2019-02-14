<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 10:30 PM
 */

namespace Test\Unit\ReferralAgreement\Message;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Form\CreateAgreementForm;
use PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\ActivateAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\CreateReferralAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PauseAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateQuantity;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateStrategy;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\AcceptInvitation;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\DeclineInvitation;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\SaveAgreementInvitee;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PublishAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateName;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadParticipantAgreements;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadUserAgreements;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByAgreementGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByUserGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByEmailAddress;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PHPUnit\Framework\TestCase;


/**
 * Class MessageFactoryTest
 *
 * @package Test\Unit\ReferralAgreement\Message
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

    /** Commands **/
    public function testCanCreateNewCreateReferralAgreement()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $formMock = $this->createMock(CreateAgreementForm::class);

        // exercise SUT
        $result = $this->messageFactory->newCreateReferralAgreement($guidMock, $formMock, $guidMock, $guidMock);

        // make assertions
        $this->assertInstanceOf(CreateReferralAgreement::class, $result);
    }

    public function testCanCreateNewPublishAgreement()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);


        // exercise SUT
        $command = $this->messageFactory->newPublishAgreement($guidMock);

        // make assertions
        $this->assertInstanceOf(PublishAgreement::class, $command);
    }

    public function testCanCreateNewUpdateName()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $name = 'Some new agreement name';

        // exercise SUT
        $command = $this->messageFactory->newUpdateName($guidMock, $name);

        // make assertions
        $this->assertInstanceOf(UpdateName::class, $command);
    }

    public function testCanCreateNewUpdateAgreementDescription()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $description = 'New test agreement description.';

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementDescription($guidMock, $description);
        
        // make assertions
        $this->assertInstanceOf(UpdateDescription::class, $command);
    }

    public function testCanCreateNewSaveAgreementInvitee()
    {
        // set up fixtures
        $inviteeGuidMock = $this->createMock(GuidInterface::class);

        $formMock = $this->createMock(ReferralAgreementInviteeForm::class);

        // exercise SUT
        $command = $this->messageFactory->newSaveAgreementInvitee($inviteeGuidMock, $formMock);

        // make assertions
        $this->assertInstanceOf(SaveAgreementInvitee::class, $command);
    }

    public function testCanCreateNewAcceptInvitation()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $command = $this->messageFactory->newAcceptInvitation($guidMock, $guidMock);
        
        // make assertions
        $this->assertInstanceOf(AcceptInvitation::class, $command);
    }

    public function testCanCreateNewDeclineInvitation()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $command = $this->messageFactory->newDeclineInvitation($guidMock, $guidMock);

        // make assertions
        $this->assertInstanceOf(DeclineInvitation::class, $command);
    }

    public function testCanCreateNewPauseAgreement()
    {
        $guidMock = $this->createMock(GuidInterface::class);
        $changeReasonMock = $this->createMock(StatusChangeReason::class);


        // exercise SUT
        $command = $this->messageFactory->newPauseAgreement($guidMock, $changeReasonMock, $guidMock);

        // make assertions
        $this->assertInstanceOf(PauseAgreement::class, $command);
    }

    public function testCanCreateNewActivateAgreement()
    {
        $agmtGuid = '01046c47-bb95-4e68-9b82-51ba62f8227f';
        $changeReason = StatusChangeReason::OWNER_REQUESTED()->getValue();
        $requestorGuid = '662402b7-acdf-44fa-a51d-27a4139a812f';


        // exercise SUT
        $command = $this->messageFactory->newActivateAgreement($agmtGuid, $changeReason, $requestorGuid);

        // make assertions
        $this->assertInstanceOf(ActivateAgreement::class, $command);
    }

    public function testCanCreateNewUpdateAgreementQuantity()
    {
        // set up fixtures
        $agmtGuid = '61d0ab03-b306-4404-87de-2023aae9a086';
        $quantity = 10;

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementQuantity($agmtGuid, $quantity);

        // make assertions
        $this->assertInstanceOf(UpdateQuantity::class, $command);

    }

    public function testCanCreateNewUpdateAgreementStrategy()
    {
        // set up fixtures
        $agmtGuid = '61d0ab03-b306-4404-87de-2023aae9a086';
        $strategy = 'weekly';

        // exercise SUT
        $command = $this->messageFactory->newUpdateAgreementStrategy($agmtGuid, $strategy);

        // make assertions
        $this->assertInstanceOf(UpdateStrategy::class, $command);

    }


    /** Queries  **/

    public function testCanCreateFindAgreementByGuid()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $this->messageFactory->newFindAgreementByGuid($guidMock);

        // make assertions
        $this->assertInstanceOf(FindByGuid::class, $query);
    }

    public function testCanCreateLoadUserAgreements()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $this->messageFactory->newLoadUserAgreements($guidMock);

        // make assertions
        $this->assertInstanceOf(LoadUserAgreements::class, $query);
    }

    public function testCanCreateLoadParticipantAgreements()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $this->messageFactory->newLoadParticipantAgreements($guidMock);

        // make assertions
        $this->assertInstanceOf(LoadParticipantAgreements::class, $query);
    }

    public function testCanCreateFindInvitationsByUserGuid()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $this->messageFactory->newFindInvitationsByUserGuid($guidMock);

        // make assertions
        $this->assertInstanceOf(FindByUserGuid::class, $query);
    }

    public function testCanCreateFindInvitationsByEmailAddress()
    {
        // set up fixtures
        $emailAddressMock = $this->createMock(EmailAddress::class);

        // exercise SUT
        $query = $this->messageFactory->newFindInvitationsByEmailAddress($emailAddressMock);

        // make assertions
        $this->assertInstanceOf(FindByEmailAddress::class, $query);
    }

    public function testCanCreateNewFindInviteeByAgreementGuid()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = $this->messageFactory->newFindInvitationsByAgreementGuid($guidMock);

        // make assertions
        $this->assertInstanceOf(FindByAgreementGuid::class, $query);
    }
}
