<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/9/18
 * Time: 6:04 AM
 */


namespace Test\Unit\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Form\CreateAgreementForm;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\CreateReferralAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\CreateReferralAgreementHandler;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use Symfony\Component\Serializer\Serializer;
use PHPUnit\Framework\TestCase;


/**
 * Class CreateReferralAgreementHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Command\Agreement
 */
class CreateReferralAgreementHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agreementGuid = '6170baf4-dc50-4a30-9b85-206d5d05d8b2';
        $userGuid = 'dc7b4853-4360-4a77-8edc-107c3664b03d';
        $companyGuid = '4acc91b5-a9f0-4cde-8de7-83c959836fce';

        $guidMock = $this->createMock(Guid::class);
        $guidMock->expects($this->exactly(4))
            ->method('value')
            ->willReturnOnConsecutiveCalls($agreementGuid, $companyGuid, $agreementGuid, $userGuid);

        $createAgmtFormMock = $this->createMock(CreateAgreementForm::class);
        $createAgmtFormMock->expects($this->once())
            ->method('getName')
            ->willReturn('Test Agreement Name');
        $createAgmtFormMock->expects($this->once())
            ->method('getDescription')
            ->willReturn('A test agreement');
        $createAgmtFormMock->expects($this->once())
            ->method('getQuantity')
            ->willReturn(10);
        $createAgmtFormMock->expects($this->once())
            ->method('getStrategy')
            ->willReturn('weekly');
        $createAgmtFormMock->expects($this->once())
            ->method('getBid')
            ->willReturn(35.00);
        $createAgmtFormMock->expects($this->once())
            ->method('getIncludedLocations')
            ->willReturn(array());
        $createAgmtFormMock->expects($this->once())
            ->method('getExcludedLocations')
            ->willReturn(array());
        $createAgmtFormMock->expects($this->once())
            ->method('getIncludedServices')
            ->willReturn(array());
        $createAgmtFormMock->expects($this->once())
            ->method('getExcludedServices')
            ->willReturn(array());


        $commandMock = $this->createMock(CreateReferralAgreement::class);
        $commandMock->expects($this->once())
            ->method('getCreateAgreementForm')
            ->willReturn($createAgmtFormMock);
        $commandMock->expects($this->exactly(2))
            ->method('getAgreementGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getCompanyGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->exactly(3))
            ->method('getUserId')
            ->willReturn($guidMock);

        $includeExcludeList = $this->createMock(IncludeExcludeList::class);
        $includeExcludeList->expects($this->exactly(4))
            ->method('addAll')
            ->willReturn($includeExcludeList);
        $agmtStatusMock = $this->createMock(AgreementStatus::class);
        $statusHistoryMock = $this->createMock(StatusHistory::class);

        $refAgmtMock = $this->createMock(ReferralAgreement::class);
        $refAgmtMock->expects($this->once())
            ->method('setOwnerGuid')
            ->with($this->equalTo($guidMock))
            ->willReturn($refAgmtMock);
        $refAgmtMock->expects($this->once())
            ->method('setIncludedLocations')
            ->willReturn($refAgmtMock);
        $refAgmtMock->expects($this->once())
            ->method('setExcludedLocations')
            ->willReturn($refAgmtMock);
        $refAgmtMock->expects($this->once())
            ->method('setIncludedServices')
            ->willReturn($refAgmtMock);
        $refAgmtMock->expects($this->once())
            ->method('setIncludedServices')
            ->willReturn($refAgmtMock);
        $refAgmtMock->expects($this->once())
            ->method('setStatusHistory')
            ->with($this->equalTo($statusHistoryMock))
            ->willReturn($refAgmtMock);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(7))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($refAgmtMock, $includeExcludeList, $includeExcludeList, $includeExcludeList, $includeExcludeList, $agmtStatusMock, $statusHistoryMock);

        $serviceMock = $this->createMock(ReferralAgreementService::class);
        $serviceMock->expects($this->once())
            ->method('createReferralAgreement')
            ->with($this->equalTo($refAgmtMock), $this->equalTo($guidMock));

        // exercise SUT
        $handler = new CreateReferralAgreementHandler($serviceMock, $serializerMock);
        $handler->__invoke($commandMock);
    }

}