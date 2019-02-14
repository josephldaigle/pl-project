<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/12/18
 * Time: 9:38 AM
 */

namespace Test\Unit\Referral;


use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ReferralService;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\ValueObject\ReferralRating;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

class ReferralServiceTest extends TestCase
{
    /**
     * @var Registry
     */
    private $workflowRegistryMock;

    /**
     * @var ReferralRepository
     */
    private $referralRepositoryMock;

    /**
     * @var GuidFactory
     */
    private $guidFactoryMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // create data access resources
        $this->workflowRegistryMock = $this->createMock(Registry::class);
        $this->referralRepositoryMock = $this->createMock(ReferralRepository::class);
        $this->guidFactoryMock = $this->createMock(GuidFactory::class);

    }

    public function provideDisputeStarValues()
    {
        return [
            [1],
            [2]
        ];
    }

    public function provideAcceptStarValues()
    {
        return [
            [3],
            [4],
            [5]
        ];
    }

    public function testCreateReferralIsSuccessfulWhenRecipientIsAgreement()
    {
        // fixtures
        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);

        $guid = $this->createMock(Guid::class);

        $this->guidFactoryMock->expects($this->once())
            ->method('generate')
            ->willReturn($guid);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);

        $referralMock->expects($this->once())
            ->method('setGuid')
            ->with($guid);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->exactly(2))
            ->method('apply')
            ->withConsecutive([$referralMock, 'create'], [$referralMock, 'acquire']);

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->createReferral($referralMock);
    }

    public function testCreateReferralAppliesCreateTransitionWhenRecipientIsContact()
    {
        // fixtures
        $contactRecipientMock = $this->createMock(ContactRecipient::class);

        $guid = $this->createMock(Guid::class);

        $this->guidFactoryMock->expects($this->once())
            ->method('generate')
            ->willReturn($guid);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($contactRecipientMock);

        $referralMock->expects($this->once())
            ->method('setGuid')
            ->with($guid);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($referralMock, 'create');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->createReferral($referralMock);
    }

    /**
     * @dataProvider provideAcceptStarValues
     */
    public function testRateReferralAppliesAcceptTransitionWhenScoreIsGreaterOrEqualToThree($values)
    {
        // fixtures
        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);

        $ratingMock = $this->createMock(ReferralRating::class);
        $ratingMock->expects($this->once())
            ->method('getScore')
            ->willReturn($values);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRating')
            ->willReturn($ratingMock);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($referralMock, 'accept');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->rateReferral($referralMock);
    }


    /**
     * @dataProvider provideDisputeStarValues
     */
    public function testRateReferralAppliesDisputeTransitionWhenAgreementRecipientScoreIsLessThanThree($values)
    {
        // fixtures
        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);

        $ratingMock = $this->createMock(ReferralRating::class);
        $ratingMock->expects($this->once())
            ->method('getScore')
            ->willReturn($values);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);
        $referralMock->expects($this->once())
            ->method('getRating')
            ->willReturn($ratingMock);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($referralMock, 'dispute');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->rateReferral($referralMock);
    }

    /**
     * @dataProvider provideDisputeStarValues
     */
    public function testRateReferralDoesNotApplyDisputeTransitionWhenContactRecipientScoreIsLessThanThree($values)
    {
        // fixtures
        $contactRecipientMock = $this->createMock(ContactRecipient::class);

        $ratingMock = $this->createMock(ReferralRating::class);
        $ratingMock->expects($this->once())
            ->method('getScore')
            ->willReturn($values);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($contactRecipientMock);
        $referralMock->expects($this->once())
            ->method('getRating')
            ->willReturn($ratingMock);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->never())
            ->method('apply');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->with($referralMock)
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->rateReferral($referralMock);
    }

    public function testResolveDisputeAppliesAdminReviewTransition()
    {
        // Fixture
        $referralMock = $this->createMock(Referral::class);

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($referralMock, 'admin_review');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->willReturn($workflowMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->resolveDispute($referralMock);
    }

    public function testFindByGuid()
    {
        // Fixture
        $guidMock = $this->createMock(Guid::class);

        $this->referralRepositoryMock->expects($this->once())
            ->method('fetchByGuid')
            ->with($guidMock);

        // SUT
        $referralService = new ReferralService($this->workflowRegistryMock, $this->referralRepositoryMock, $this->guidFactoryMock);
        $referralService->findByGuid($guidMock);
    }
}