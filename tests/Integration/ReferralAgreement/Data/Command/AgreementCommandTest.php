<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/6/18
 * Time: 6:52 AM
 */

namespace Test\Integration\ReferralAgreement\Data\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\LocationType;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\ReferralAgreement\ValueObject\ServiceType;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class AgreementCommandTest
 *
 * @package Test\Integration\ReferralAgreement\Data\Command
 */
class AgreementCommandTest extends WebDatabaseTestCase
{
    /**
     * @var MessageBusInterface
     */
    private $dataBus;

    /**
     * @var MessageFactory
     */
    private $msgFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        // configure test data set
        $this->configureDataSet([
            'Person',
            'User',
            'EmailAddress',
            'L_EmailAddressType',
            'R_PersonEmailAddress',
            'Company',
            'ReferralAgreement',
            'ReferralAgreementInvitee',
            'ReferralAgreementLocation',
            'ReferralAgreementService',
            'ReferralAgreementStatus',
            'L_ReferralAgreementStatusReason',
            'L_UserRole',
            'R_UserCompanyRole',
        ]);

        parent::setUp();

        // fetch services
        $this->dataBus    = $this->diContainer->get('messenger.bus.mysql');
        $this->msgFactory = $this->diContainer->get('PapaLocal\ReferralAgreement\Data\MessageFactory');
    }

    public function testCanCreateAgreement()
    {
        // set up fixtures
        $begTableRowCount = $this->getConnection()->getRowCount('ReferralAgreement');

        $agmtGuid        = 'ec9bf56c-8f90-4576-8baf-167a81ef9eb9';
        $companyGuid     = '90a84315-f83d-4f1b-8c31-1cdc86453f86';
        $agmtName        = 'Test Agreement';
        $agmtDescription = 'This is a test agreement.';
        $quantity        = 5;
        $strategy        = 'weekly';
        $bid             = 35.00;
        $ownerGuid       = '6b51ad65-b5f7-4cd2-a51f-60b51f397600';

        $referralAgreement = new ReferralAgreement(
            new Guid($agmtGuid),
            new Guid($companyGuid),
            $agmtName,
            $agmtDescription,
            $quantity,
            $strategy,
            $bid,
            new Guid($ownerGuid)
        );

        $saveCommand = $this->msgFactory->newSaveAgreement($referralAgreement);

        // exercise SUT
        $this->dataBus->dispatch($saveCommand);

        // make assertions
        $this->assertTableRowCount('ReferralAgreement', $begTableRowCount + 1);

    }

    public function testCanPauseAgreement()
    {
        // set up fixtures
        $begTableRowCount = $this->getConnection()->getRowCount('ReferralAgreementStatus');

        // fetch an agreement to update
        $agmtRow = $this->getConnection()
                        ->createQueryTable('agreement', 'SELECT * FROM v_referral_agreement LIMIT 1')
                        ->getRow(0);

        $reasonId = $this->getConnection()
                         ->createQueryTable('reason',
                             'SELECT * FROM L_ReferralAgreementStatusReason WHERE reason = \''.StatusChangeReason::CREATED()->getValue().'\'')
                         ->getRow(0)['id'];

        // fetch a user to act as author
        $authorRow = $this->getConnection()
                          ->createQueryTable('user', 'SELECT * FROM v_user LIMIT 1')
                          ->getRow(0);


        $agreementStatus = new AgreementStatus(new Guid($agmtRow['guid']), Status::INACTIVE(),
            StatusChangeReason::CREATED(), new Guid($authorRow['userGuid']));

        $command = $this->msgFactory->newUpdateAgreementStatus($agreementStatus);

        // exercise SUT
        $this->dataBus->dispatch($command);

        // make assertions
        $this->assertTableRowCount('ReferralAgreementStatus', $begTableRowCount + 1);
        $this->getConnection()
             ->createQueryTable('ref_agmt_status', 'SELECT * FROM ReferralAgreementStatus')
             ->assertContainsRow(array(
                 'agreementId' => $agmtRow['id'],
                 'status'      => Status::INACTIVE()->getValue(),
                 'reasonId'    => $reasonId,
                 'updatedBy'   => $authorRow['userId'],
             ));
    }

    public function testCanUpdateLocations()
    {
        // set up fixtures
        $agmtArr = $this->getConnection()
                        ->createQueryTable('agreement', 'SELECT * FROM v_referral_agreement LIMIT 1')
                        ->getRow(0);

        $tableRowCount = $this->getConnection()->getRowCount('v_referral_agreement_location');

        $expectedDeleteCount = $this->getConnection()->getRowCount('v_referral_agreement_location',
            'agreementId = \''.$agmtArr['id'].'\'');

        $agreementGuid = new Guid($agmtArr['guid']);

        $location1 = new Location('Somewhere, GA', LocationType::INCLUDE ());
        $location2 = new Location('Anywhere, GA', LocationType::EXCLUDE());

        $locationsList = new IncludeExcludeList(array($location1, $location2));

        $command = $this->msgFactory->newUpdateLocations($agreementGuid, $locationsList);

        // exercise SUT
        $this->dataBus->dispatch($command);

        // make assertions
        $this->assertTableRowCount('v_referral_agreement_location', ($tableRowCount - $expectedDeleteCount) + 2);
    }

    public function testCanUpdateServices()
    {
        // set up fixtures
        $agmtArr = $this->getConnection()
                        ->createQueryTable('agreement', 'SELECT * FROM v_referral_agreement LIMIT 1')
                        ->getRow(0);

        $tableRowCount = $this->getConnection()->getRowCount('v_referral_agreement_service');

        $expectedDeleteCount = $this->getConnection()->getRowCount('v_referral_agreement_service',
            'agreementId = \''.$agmtArr['id'].'\'');

        $agreementGuid = new Guid($agmtArr['guid']);

        $service1 = new Service('Some service', ServiceType::INCLUDE ());
        $service2 = new Service('Another service', ServiceType::EXCLUDE());

        $servicesList = new IncludeExcludeList(array($service1, $service2));

        $command = $this->msgFactory->newUpdateServices($agreementGuid, $servicesList);

        // exercise SUT
        $this->dataBus->dispatch($command);

        // make assertions
        $this->assertTableRowCount('v_referral_agreement_service', ($tableRowCount - $expectedDeleteCount) + 2);
    }
}