<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/8/18
 * Time: 7:07 PM
 */

namespace PapaLocal\Referral\Event;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\IdentityAccess\Event\UserRegistered;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;


/**
 * Class ContactRegisteredSubscriberTest
 * @package PapaLocal\Referral\Event
 */
class ContactRegisteredSubscriberTest extends TestCase
{
    public function testCanAcquireReferralWithContactRecipientSuccessfully()
    {
        $guidMock = $this->createMock(GuidInterface::class);

        $contactRecipient = $this->createMock(ContactRecipient::class);
        $contactRecipient->expects($this->once())
            ->method('setContactGuid')
            ->with($guidMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($contactRecipient);

        $iteratorMock = $this->createMock(\Traversable::class);
        $iteratorMock->expects($this->exactly(2))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);
        $iteratorMock->expects($this->once())
            ->method('current')
            ->willReturn($referralMock);

        $referralCollectionMock = $this->createMock(Collection::class);
        $referralCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn($iteratorMock);

        $emailAddressMock = $this->createMock(EmailAddress::class);

        $repositoryMock = $this->createMock(ReferralRepository::class);
        $repositoryMock->expects($this->once())
            ->method('fetchByRecipientEmailAddress')
            ->with($emailAddressMock)
            ->willReturn($referralCollectionMock);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($referralMock, 'acquire');

        $workflowRegistryMock = $this->createMock(Registry::class);
        $workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        $eventMock = $this->createMock(UserRegistered::class);
        $eventMock->expects($this->once())
            ->method('getUsername')
            ->willReturn($emailAddressMock);
        $eventMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($guidMock);

        $contactRegisteredSubscriber = new ContactRegisteredSubscriber($repositoryMock, $workflowRegistryMock);
        $contactRegisteredSubscriber->acquireReferral($eventMock);
    }
}