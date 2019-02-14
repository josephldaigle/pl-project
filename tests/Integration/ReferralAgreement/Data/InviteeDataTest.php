<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/20/18
 */


namespace Test\Integration\ReferralAgreement\Data;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class InviteeDataTest.
 *
 * @package Test\Integration\ReferralAgreement\Data
 */
class InviteeDataTest extends WebDatabaseTestCase
{
    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
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

        // boot kernel
        self::bootKernel();

        // fetch services
        $this->inviteeRepository = self::$container->get('PapaLocal\ReferralAgreement\Data\InviteeRepository');
    }

    public function testCanFindInviteeByGuid()
    {
        // set up fixtures
        $inviteeGuid = $this->getConnection()
            ->createQueryTable('invitee_guid', 'SELECT guid FROM ReferralAgreementInvitee LIMIT 1')
            ->getRow(0)['guid'];

        $guid = new Guid($inviteeGuid);

        // exercise SUT
        $invitee = $this->inviteeRepository->findByGuid($guid);

        // make assertions
        $this->assertInstanceOf(ReferralAgreementInvitee::class, $invitee);
    }

    public function testCanFindAllInviteesByAgreementGuid()
    {
        // set up fixtures
        $agreementGuid = $this->getConnection()
            ->createQueryTable('agmt_guid', 'SELECT agreementGuid FROM v_referral_agreement_invitee LIMIT 1')
            ->getRow(0)['agreementGuid'];

        $guid = new Guid($agreementGuid);

        // exercise SUT
        $inviteeList = $this->inviteeRepository->findAllByAgreementGuid($guid);

        // make assertions
        $this->assertInstanceOf(Collection::class, $inviteeList, 'unexpected type');
        $this->assertEquals(2, $inviteeList->count(), 'unexpected count');
    }

    public function testCanFindAllInviteesByEmailAddress()
    {
        // set up fixtures
        $emailAddress = $this->getConnection()
            ->createQueryTable('email', 'SELECT emailAddress FROM v_referral_agreement_invitee LIMIT 1')
            ->getRow(0)['emailAddress'];
        $expectedRowCount = intval($this->getConnection()
                                        ->createQueryTable('exp_row_count', 'SELECT COUNT(emailAddress) as \'count\' FROM v_referral_agreement_invitee WHERE emailAddress = \'' . $emailAddress . '\'')
            ->getRow(0)['count']);

        $emailAddress = new EmailAddress($emailAddress, EmailAddressType::PERSONAL());

        // exercise SUT
        $inviteeList = $this->inviteeRepository->findAllByEmailAddress($emailAddress);

        // make assertions
        $this->assertInstanceOf(Collection::class, $inviteeList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $inviteeList->count(), 'unexpected result count');
    }

    public function testCanFindAllInviteesByUserGuid()
    {
        // set up fixtures
        $userGuid = $this->getConnection()
                             ->createQueryTable('user_guid', 'SELECT userGuid FROM v_referral_agreement_invitee LIMIT 1')
                             ->getRow(0)['userGuid'];
        $expectedRowCount = intval($this->getConnection()
                                        ->createQueryTable('exp_row_count', 'SELECT COUNT(userGuid) as \'count\' FROM v_referral_agreement_invitee WHERE userGuid = \'' . $userGuid . '\'')
                                        ->getRow(0)['count']);

        $userGuid = new Guid($userGuid);

        // exercise SUT
        $inviteeList = $this->inviteeRepository->findAllByUserGuid($userGuid);

        // make assertions
        $this->assertInstanceOf(Collection::class, $inviteeList, 'unexpected type');
        $this->assertEquals($expectedRowCount, $inviteeList->count(), 'unexpected result count');
    }
}