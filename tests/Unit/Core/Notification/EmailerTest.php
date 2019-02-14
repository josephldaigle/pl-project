<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/10/18
 * Time: 10:24 AM
 */

namespace Test\Unit\Core\Notification;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\Notification\EmailMessageInterface;
use PapaLocal\Data\Ewebify;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class EmailerTest
 *
 * @package Test\Unit\Core\Notification
 */
class EmailerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $emailBuilderMock;

    /**
     * @var MockObject
     */
    private $swiftMailerMock;

    /**
     * @var MockObject
     */
    private $loggerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->emailBuilderMock = $this->createMock(EmailMessageBuilder::class);
        $this->swiftMailerMock = $this->createMock(\Swift_Mailer::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

    }

    public function testCanInstantiate()
    {
        // exercise SUT
        $emailer = new Emailer($this->emailBuilderMock, $this->swiftMailerMock, $this->loggerMock);

        // make assertions
        $this->assertInstanceOf(Emailer::class, $emailer, 'unexpected type');
    }

    public function testSendReturnsTrueOnSuccess()
    {
        // set up fixtures
        $subject = 'This is a test email from PapaLocal.';
        $sender = Ewebify::ADMIN_EMAIL;
        $recipients = array('test@papalocal.com');
        $body = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $emailMessageMock = $this->createMock(EmailMessageInterface::class);
        $emailMessageMock->expects($this->once())
                         ->method('getSubject')
                         ->willReturn($subject);
        $emailMessageMock->expects($this->once())
                         ->method('getSender')
                         ->willReturn($sender);
        $emailMessageMock->expects($this->once())
                         ->method('getRecipients')
                         ->willReturn($recipients);
        $emailMessageMock->expects($this->once())
                         ->method('getBody')
                         ->willReturn($body);
        $emailer = new Emailer($this->emailBuilderMock, $this->swiftMailerMock, $this->loggerMock);

        $this->swiftMailerMock->expects($this->once())
                              ->method('send')
                              ->willReturn(true);

        $this->loggerMock->expects($this->never())
                         ->method('debug');

        // exercise SUT
        $result = $emailer->send($emailMessageMock);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals(count($recipients), $result, 'unexpected value');
    }

    public function testSendProvidesFailedRecipientsOnFailure()
    {
        // set up fixtures
        $subject = 'This is a test email from PapaLocal.';
        $sender = Ewebify::ADMIN_EMAIL;
        $recipients = array('test@papalocal.com');
        $body = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $emailMessageMock = $this->createMock(EmailMessageInterface::class);
        $emailMessageMock->expects($this->exactly(2))
                         ->method('getSubject')
                         ->willReturn($subject);
        $emailMessageMock->expects($this->once())
                         ->method('getSender')
                         ->willReturn($sender);
        $emailMessageMock->expects($this->once())
                         ->method('getRecipients')
                         ->willReturn($recipients);
        $emailMessageMock->expects($this->once())
                         ->method('getBody')
                         ->willReturn($body);
        $emailer = new Emailer($this->emailBuilderMock, $this->swiftMailerMock, $this->loggerMock);

        $this->swiftMailerMock->expects($this->once())
            ->method('send')
            ->willReturn(0);

        // exercise SUT
        $failedRecipients = array();
        $result = $emailer->send($emailMessageMock, $failedRecipients);

        // make assertions
        $this->assertTrue(is_array($failedRecipients), 'unexpected failures type');
        $this->assertCount(1, $failedRecipients, 'unexpected failures count');
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals(0, $result, 'unexpected value');
    }

}