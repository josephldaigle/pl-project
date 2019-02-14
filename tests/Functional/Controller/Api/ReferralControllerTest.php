<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/19/18
 * Time: 5:00 PM
 */

namespace Test\Functional\Controller\Api;


use PapaLocal\Test\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Csrf\CsrfTokenManager;


/**
 * Class ReferralControllerTest
 * @package Test\Functional\Controller\Api
 */
class ReferralControllerTest extends WebDatabaseTestCase
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

    public function testCanCreateReferralWithAgreementRecipient()
    {
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('Referral');

        $activeAgreementGuid = $this->getConnection()
            ->createQueryTable('status', "SELECT agreementGuid FROM v_referral_agreement_current_status WHERE status='Active' LIMIT 1")
            ->getRow(0)['agreementGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addReferral')->getValue(),
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'phoneNumber' => '8787899889',
            'emailAddress' => 'guy@tester.com',
            'address' => array(
                "streetAddress" => "349 Georgia Avenue",
                "city" => "Silver Spring",
                "state" => "Maryland",
                "postalCode" => "20902",
                "country" => "United States"
            ),
            'about' => 'about referral',
            'note' => '',
            'agreementId' => $activeAgreementGuid,
        );

        // exercise SUT
        $this->client->request('POST', '/referral/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $referralStatusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE agreementGuid='" . $activeAgreementGuid . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('Referral', $begRowCount + 1, 'Referral table row count');
        $this->assertSame('acquired', $referralStatusRow['currentPlace']);
    }

    public function testCanCreateReferralWithContactRecipient()
    {
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('Referral');

        $emailAddress = 'alex@Johnson.com';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('addReferral')->getValue(),
            'firstName' => 'Guy',
            'lastName' => 'Tester',
            'phoneNumber' => '8787899889',
            'emailAddress' => 'guy@tester.com',
            'address' => array(
                "streetAddress" => "349 Georgia Avenue",
                "city" => "Silver Spring",
                "state" => "Maryland",
                "postalCode" => "20902",
                "country" => "United States"
            ),
            'about' => 'about referral',
            'note' => '',
            'recipientFirstName' => 'Alex',
            'recipientLastName' => 'Johnson',
            'recipientPhoneNumber' => '8769879876',
            'recipientEmailAddress' => $emailAddress,

        );

        // exercise SUT
        $this->client->request('POST', '/referral/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE recipientEmailAddress='" . $emailAddress . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertTableRowCount('Referral', $begRowCount + 1, 'Referral table row count');
        $this->assertSame('created', $statusRow['currentPlace']);

    }

    public function testCanRateReferralHigherOrEqualToThreeStars()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT guid FROM v_referral WHERE currentPlace = "acquired" LIMIT 1')
            ->getRow(0)['guid'];
        $score = '3';
        $feedback = 'This is a referral feedback.';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('rateReferral')->getValue(),
            'referralGuid' => $guid,
            'referralRate' => $score,
            'referralFeedback' => $feedback
        );

        // exercise SUT
        $this->client->request('POST', '/referral/rate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE guid='" . $guid . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('finalized', $statusRow['currentPlace']);
        $this->assertSame($score, $statusRow['score']);
        $this->assertSame($feedback, $statusRow['feedback']);
    }

    public function testCanRateReferralWithAgreementRecipientLowerThanThreeStars()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT guid FROM v_referral WHERE currentPlace = "acquired" AND agreementOwnerGuid IS NOT NULL LIMIT 1')
            ->getRow(0)['guid'];
        $score = '2';
        $feedback = 'This is a referral feedback.';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('rateReferral')->getValue(),
            'referralGuid' => $guid,
            'referralRate' => $score,
            'referralFeedback' => $feedback
        );

        // exercise SUT
        $this->client->request('POST', '/referral/rate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE guid='" . $guid . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('disputed', $statusRow['currentPlace']);
        $this->assertSame($score, $statusRow['score']);
        $this->assertSame($feedback, $statusRow['feedback']);
    }

    public function testCannotRateReferralWithContactRecipientLowerThanThreeStars()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT guid FROM v_referral WHERE contactGuid IS NOT NULL AND currentPlace = "acquired" LIMIT 1')
            ->getRow(0)['guid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('rateReferral')->getValue(),
            'referralGuid' => $guid,
            'referralRate' => '1',
            'referralFeedback' => 'This is a referral feedback.'
        );

        // exercise SUT
        $this->client->request('POST', '/referral/rate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testCannotResolveDisputeWithNoneAdminUser()
    {
        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT providerUserGuid FROM v_referral LIMIT 1')
            ->getRow(0)['providerUserGuid'];

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('resolveDispute')->getValue(),
            'referralGuid' => $guid,
            'reviewerGuid' => 'b1b9adbc-ca43-4665-9b6e-66ce3c4a6606',
            'resolution' => 'approved',
            'reviewerNote' => 'This is a reviewer feedback.'
        );

        // exercise SUT
        $this->client->request('POST', '/referral/dispute', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        // make assertions
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testCanResolveDisputeWithApproval()
    {
        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'yacouba@ewebify.com',
            'PHP_AUTH_PW'   => 'eWebify116**',
        ));
        $this->client->followRedirects();
        $this->client->enableProfiler();

        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT guid FROM v_referral WHERE currentPlace = "disputed" LIMIT 1')
            ->getRow(0)['guid'];

        $referralGuid = $guid;
        $reviewerGuid = 'b1b9adbc-ca43-4665-9b6e-66ce3c4a6606';
        $resolution = 'approved';
        $reviewerNote = 'This is a referral feedback.';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('resolveDispute')->getValue(),
            'referralGuid' => $referralGuid,
            'reviewerGuid' => $reviewerGuid,
            'resolution' => $resolution,
            'reviewerNote' => $reviewerNote
        );

        // exercise SUT
        $this->client->request('POST', '/referral/dispute', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE guid='" . $referralGuid . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('finalized', $statusRow['currentPlace']);
        $this->assertSame($reviewerGuid, $statusRow['reviewerGuid']);
        $this->assertSame($resolution, $statusRow['resolution']);
        $this->assertSame($reviewerNote, $statusRow['reviewerNote']);
    }

    public function testCanResolveDisputeWithDenial()
    {
        $this->client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'yacouba@ewebify.com',
            'PHP_AUTH_PW'   => 'eWebify116**',
        ));
        $this->client->followRedirects();
        $this->client->enableProfiler();

        // set up fixtures
        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT guid FROM v_referral WHERE currentPlace = "disputed" LIMIT 1')
            ->getRow(0)['guid'];

        $referralGuid = $guid;
        $reviewerGuid = 'b1b9adbc-ca43-4665-9b6e-66ce3c4a6606';
        $resolution = 'denied';
        $reviewerNote = 'This is a referral feedback.';

        $postData = array(
            '_csrf_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('resolveDispute')->getValue(),
            'referralGuid' => $referralGuid,
            'reviewerGuid' => $reviewerGuid,
            'resolution' => $resolution,
            'reviewerNote' => $reviewerNote
        );

        // exercise SUT
        $this->client->request('POST', '/referral/dispute', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM v_referral WHERE guid='" . $referralGuid . "'")
            ->getRow(0);

        // make assertions
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('finalized', $statusRow['currentPlace']);
        $this->assertSame($reviewerGuid, $statusRow['reviewerGuid']);
        $this->assertSame($resolution, $statusRow['resolution']);
        $this->assertSame($reviewerNote, $statusRow['reviewerNote']);
    }
}