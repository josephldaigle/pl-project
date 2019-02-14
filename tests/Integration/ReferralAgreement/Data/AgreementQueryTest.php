<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/29/18
 * Time: 2:41 PM
 */

namespace Test\Integration\ReferralAgreement\Data;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class AgreementQueryTest
 *
 * @package Test\Integration\ReferralAgreement\Data
 */
class AgreementQueryTest extends WebDatabaseTestCase
{
    /**
     * @var ReferralAgreementRepository
     */
    private $agreementRepository;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->configureDataSet([]);

        parent::setUp();

        // fetch services
        $this->agreementRepository = $this->diContainer->get('PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository');
    }

    public function testCanLoadParticipantAgreements()
    {
        $this->markTestIncomplete();

        // set up fixtures
        $userGuid = $this->getConnection()
            ->createQueryTable('participant', 'SELECT userGuid FROM v_referral_agreement_invitee LIMIT 1')
            ->getRow(0)['userGuid'];
            
        $participantGuid = new Guid($userGuid);

        // exercise SUT
        $agreements = $this->agreementRepository->loadParticipantAgreements($participantGuid);

        // make assertions

    }
}