<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/7/18
 * Time: 11:25 AM
 */

namespace Test\Integration\Referral\Event;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\IdentityAccess\Event\UserRegistered;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class ContactRegisteredSubscriberTest
 * @package Test\Integration\Referral\Event
 */
class ContactRegisteredSubscriberTest extends WebDatabaseTestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([]);

        parent::setUp();
    }

    public function testContactCanAcquireReferralAfterRegistration()
    {
        // set up fixtures
        $emailAddress = $this->getConnection()
            ->createQueryTable('emailAddress', 'SELECT username FROM v_user WHERE username = "dstevens@papalocal.com" LIMIT 1')
            ->getRow(0)['username'];

        $guid = $this->getConnection()
            ->createQueryTable('guid', 'SELECT userGuid FROM v_user WHERE username = "' . $emailAddress . '" LIMIT 1')
            ->getRow(0)['userGuid'];

        $firstName = $this->getConnection()
            ->createQueryTable('firstName', 'SELECT firstName FROM v_user WHERE username = "' . $emailAddress . '" LIMIT 1')
            ->getRow(0)['firstName'];

        $lastName = $this->getConnection()
            ->createQueryTable('lastName', 'SELECT lastName FROM v_user WHERE username = "' . $emailAddress . '" LIMIT 1')
            ->getRow(0)['lastName'];

        $username = new EmailAddress($emailAddress, EmailAddressType::USERNAME());
        $userRegisteredEvent = new UserRegistered(
            new Guid($guid),
            $username,
            $firstName,
            $lastName
        );

        $eventDispatcher = $this->diContainer->get('event_dispatcher');
        $eventDispatcher->dispatch(UserRegistered::class, $userRegisteredEvent);

        $statusRow = $this->getConnection()
            ->createQueryTable('status', "SELECT * FROM Referral WHERE recipientEmailAddress='". $emailAddress ."'")
            ->getRow(0);

        $this->assertSame($userRegisteredEvent->getUserGuid()->value(), $statusRow['contactGuid']);
        $this->assertSame('acquired', $statusRow['currentPlace']);
    }
}