<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/12/18
 * Time: 8:51 PM
 */

namespace Test\Functional\Controller\Api;

use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class ReferralAgreementControllerTest
 *
 * @package Test\Functional\Controller\Api
 */
class ReferralAgreementControllerTest extends WebDatabaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CsrfTokenManager
     */
    private $csrfTokenManager;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([]);

        parent::setUp();

        // create an authenticated client
        self::bootKernel();

        $this->csrfTokenManager = self::$container->get('security.csrf.token_manager');

        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'lgroom@papalocal.com',
            'PHP_AUTH_PW'   => 'eWebify116**',
        ));
        $this->client->followRedirects();
        $this->client->enableProfiler();
    }

    public function testCanCreateReferralAgreement()
    {
        // set up fixtures
        $begRaRowCount = $this->getConnection()->getRowCount('ReferralAgreement');
        $begLocRowCount = $this->getConnection()->getRowCount('ReferralAgreementLocation');
        $begSvcRowCount = $this->getConnection()->getRowCount('ReferralAgreementService');

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('createReferralAgreement')->getValue(),
            'name' => 'Test Agreement Name',
            'description' => 'Test agreement description.',
            'strategy' => 'weekly',
            'bid' => 30.00,
            'includedLocations' => array(
                'Somewhere, GA'
            ),
            'includedServices' => array(
                'Appliance Repair'
            )
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('ReferralAgreement', $begRaRowCount + 1, 'ReferralAgreement table row count');
        $this->assertTableRowCount('ReferralAgreementLocation', $begLocRowCount + 1, 'ReferralAgreementLocation table row count');
        $this->assertTableRowCount('ReferralAgreementService', $begSvcRowCount + 1, 'ReferralAgreementService table row count');
    }

    public function testCreateReferralAgreementReturnsErrorWhenRequestNotValid()
    {
        // set up fixtures
        $begRaRowCount = $this->getConnection()->getRowCount('ReferralAgreement');
        $begLocRowCount = $this->getConnection()->getRowCount('ReferralAgreementLocation');
        $begSvcRowCount = $this->getConnection()->getRowCount('ReferralAgreementService');

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('createReferralAgreement')->getValue(),
            'name' => 'Test Agreement Name',
            'includedLocations' => array(
                'Somewhere, GA'
            ),
            'includedServices' => array(
                'Appliance Repair'
            )
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('ReferralAgreement', $begRaRowCount, 'ReferralAgreement table row count');
        $this->assertTableRowCount('ReferralAgreementLocation', $begLocRowCount, 'ReferralAgreementLocation table row count');
        $this->assertTableRowCount('ReferralAgreementService', $begSvcRowCount, 'ReferralAgreementService table row count');
    }

    public function testCanUpdateAgreementName()
    {
        // set up fixtures
        $agreementGuid = $this->getConnection()
            ->createQueryTable('agreement_guid', 'SELECT guid FROM v_referral_agreement LIMIT 1')
            ->getRow(0)['guid'];

        $newName = 'New Agreement Name';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('referralAgreementName')->getValue(),
            'id' => $agreementGuid,
            'name' => $newName,
        );

        // exercise SUTs
        $this->client->request('POST', '/agreement/name/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // assert response code
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // assert db change
        $this->assertEquals($newName, $this->getConnection()
            ->createQueryTable('agmt_name', 'SELECT name FROM v_referral_agreement WHERE guid = \'' . $agreementGuid . '\'')
        ->getRow(0)['name'], 'unexpected database value');
    }

    public function testCanUpdateAgreementDescription()
    {
        // set up fixtures
        $agreementGuid = $this->getConnection()
            ->createQueryTable('agreement_guid', 'SELECT guid FROM v_referral_agreement LIMIT 1')
            ->getRow(0)['guid'];

        $newDescription = 'New test agreement description';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('referralAgreementDescription')->getValue(),
            'agreementGuid' => $agreementGuid,
            'description' => $newDescription,
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/description/update', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // assert response code
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // assert db change
        $this->assertEquals($newDescription, $this->getConnection()
            ->createQueryTable('agmt_desc', 'SELECT description FROM v_referral_agreement WHERE guid = \'' . $agreementGuid . '\'')
            ->getRow(0)['description'], 'unexpected database value');
    }

    /**
     *  TEST REFERRAL AGREEMENT INVITEE FEATURES
     */
    public function testCanAddNonUserInviteeToAgreementDuringCreation()
    {
        // set up fixtures
        $begInvRowCount = $this->getConnection()->getRowCount('v_referral_agreement_invitee');
        $agreementGuid = $this->getConnection()
            ->createQueryTable('agreement_guid', 'SELECT guid FROM v_referral_agreement LIMIT 1')
            ->getRow(0)['guid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addReferralAgreementInvitee')->getValue(),
            'agreementId' => $agreementGuid,
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'message' => 'Guy, sell me referrals.',
            'phoneNumber' => '',
            'emailAddress' => 'gtester@papalocal.com'
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/invitee/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // assert response code
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // assert db change
        $this->assertTableRowCount('v_referral_agreement_invitee', $begInvRowCount + 1);
    }

    public function testCanAddUserInviteeToAgreementDuringAgreementCreation()
    {
        // set up fixtures
        $begInvRowCount = $this->getConnection()->getRowCount('v_referral_agreement_invitee');
        $agreementRecord = $this->getConnection()
            ->createQueryTable('agreement_guid', 'SELECT * FROM v_referral_agreement LIMIT 1')
            ->getRow(0);
        $agreementGuid = $agreementRecord['guid'];

        $userRecord = $this->getConnection()
            ->createQueryTable('user', 'SELECT * FROM v_user WHERE userGuid = \'' . $agreementRecord['ownerGuid'] . '\' LIMIT 1')
            ->getRow(0);

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addReferralAgreementInvitee')->getValue(),
            'agreementId' => $agreementGuid,
            'firstName' => $userRecord['firstName'],
            'lastName' => $userRecord['lastName'],
            'message' => 'Guy, sell me referrals.',
            'phoneNumber' => 3124175412,
            'emailAddress' => $userRecord['username']
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/invitee/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // assert response code
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());


        // assert db change
        $this->assertTableRowCount('v_referral_agreement_invitee', $begInvRowCount + 1);
    }

    public function testCanAddUserInviteeFromFeedToAgreement()
    {
        // set up fixtures
        $begInvRowCount = $this->getConnection()->getRowCount('v_referral_agreement_invitee');
        $agreementRecord = $this->getConnection()
                                ->createQueryTable('agreement_guid', 'SELECT * FROM v_referral_agreement LIMIT 1')
                                ->getRow(0);
        $agreementGuid = $agreementRecord['guid'];
        $userRecord = $this->getConnection()
                           ->createQueryTable('user', 'SELECT * FROM v_user WHERE userGuid = \'' . $agreementRecord['ownerGuid'] . '\' LIMIT 1')
                           ->getRow(0);

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addReferralAgreementInvitee')->getValue(),
            'context' => 'addInviteeFromFeed',
            'agreementId' => $agreementGuid,
            'isLast' => true,
            'firstName' => $userRecord['firstName'],
            'lastName' => $userRecord['lastName'],
            'message' => 'Guy, sell me referrals.',
            'phoneNumber' => '',
            'emailAddress' => $userRecord['username']
        );

        // exercise SUT
        $this->client->request('POST', '/agreement/invitee/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // assert response code
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // assert db change
        $this->assertTableRowCount('v_referral_agreement_invitee', $begInvRowCount + 1);
    }
}