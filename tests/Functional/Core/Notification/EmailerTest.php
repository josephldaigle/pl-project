<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/10/18
 * Time: 11:12 AM
 */

namespace Test\Functional\Core\EmailerTest;


use PapaLocal\Core\Notification\EmailerInterface;
use PapaLocal\Core\Notification\EmailMessage;
use PapaLocal\Data\Ewebify;
use PapaLocal\Test\WebTestCase;


/**
 * Class EmailerTest
 *
 * @package Test\Functional\Core\EmailerTest
 */
class EmailerTest extends WebTestCase
{
    /**
     * @var EmailerInterface
     */
    private $emailer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot the kernel
        self::bootKernel();

        // fetch emailer service
        $this->emailer = static::$kernel->getContainer()->get('papalocal_core.emailer');
    }

    public function testCanSendEmailUsingStringMessage()
    {
        // set up fixtures
        $sender = Ewebify::ADMIN_EMAIL;
        $recipients = array('test@papalocal.com');
        $ccList = array();
        $subject = 'Test message from PapaLocal.';
        $body = '<p>This is a test message. Delete it and do not respond.</p>';
        $contentType = 'text/html';

        $emailMessage = new EmailMessage($sender, $recipients, $ccList, $subject, $body, $contentType);

        // exercise SUT
        $result = $this->emailer->send($emailMessage);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals($result, 1, 'unexpected value');
    }

    public function testCanFetchAndUserBuilderToSendMessage()
    {
        // set up fixtures
        $sender = Ewebify::ADMIN_EMAIL;
        $recipients = array('test@papalocal.com');
        $subject = 'Test message from PapaLocal.';
        $body = '<p>This is a test message. Delete it and do not respond.</p>';
        $contentType = 'text/html';

        $emailMessage = $this->emailer->getMessageBuilder()
                                      ->sendTo($recipients[0])
                                      ->from($sender)
                                      ->subject($subject)
                                      ->message($body)
                                      ->contentType($contentType)
                                      ->build();

        // exercise SUT
        $result = $this->emailer->send($emailMessage);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals($result, 1, 'unexpected value');
    }

    /**
     * @depends testCanFetchAndUserBuilderToSendMessage
     */
    public function testCanSendEmailUsingTwigTemplate()
    {
        // set up fixtures
        $sender = Ewebify::ADMIN_EMAIL;
        $recipients = array('test@papalocal.com');
        $subject = 'Test message from PapaLocal.';
        $templateName = 'emailTemplate.html.twig';
        $templateArgs = array(
            'args' => array(
                'valueOne' => 'test value one',
                'valuleTwo' => 'test value two'
            )
        );
        $contentType = 'text/html';

        $emailMessage = $this->emailer->getMessageBuilder()
                                      ->sendTo($recipients[0])
                                      ->from($sender)
                                      ->subject($subject)
                                      ->usingTwigTemplate($templateName, $templateArgs)
                                      ->contentType($contentType)
                                      ->build();

        // exercise SUT
        $result = $this->emailer->send($emailMessage);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals($result, 1, 'unexpected value');
    }
}