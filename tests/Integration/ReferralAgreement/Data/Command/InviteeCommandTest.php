<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/18
 * Time: 7:14 PM
 */

namespace Test\Integration\ReferralAgreement\Data\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class InviteeCommandTest
 *
 * @package Test\Integration\ReferralAgreement\Data\Command
 */
class InviteeCommandTest extends WebDatabaseTestCase
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

    public function testCanMarkInvitationAsRead()
    {
        $inviteeGuidVal = $this->getConnection()
                               ->createQueryTable('invitee', 'SELECT * FROM ReferralAgreementInvitee LIMIT 1')
                               ->getRow(0)['guid'];

        $inviteeGuid = new Guid($inviteeGuidVal);

        // exercise SUT
        $command = $this->msgFactory->newMarkInvitationSent($inviteeGuid);
        $this->dataBus->dispatch($command);

        // make assertions
        $timeSent = $this->getConnection()
                         ->createQueryTable('post_edit',
                             'SELECT * FROM ReferralAgreementInvitee WHERE guid LIKE \''.$inviteeGuidVal.'\'')
                         ->getRow(0)['timeSent'];

        $this->assertNotEmpty($timeSent);

    }
}