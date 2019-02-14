<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/10/18
 * Time: 7:26 AM
 */

namespace Test\Unit\Core\Notification;


use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\Notification\EmailMessageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


/**
 * Class EmailMessageBuilderTest
 *
 * @package Test\Unit\Core\Notification
 */
class EmailMessageBuilderTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $twigEnvMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->twigEnvMock = $this->createMock(\Twig_Environment::class);
    }

    public function testCanInstantiate()
    {
        // exercise SUT
        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // make assertions
        $this->assertInstanceOf(EmailMessageBuilder::class, $builder, 'unexpected type');
    }

    public function testBuildIsSuccessfulUsingStringBody()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $subject = 'This is a test email from PapaLocal.';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->subject($subject)
                                ->message($message)
                                ->build();

        // make assertions
        $this->assertInstanceOf(EmailMessageInterface::class, $emailMessage, 'unexpected type');
        $this->assertArraySubset($emailMessage->getRecipients(), array($recipient), 'unexpected recipients');
        $this->assertSame($emailMessage->getSubject(), $subject, 'unexpected subject');
        $this->assertSame($emailMessage->getBody(), $message, 'unexpected body');
    }

    public function testBuildIsSuccessfulUsingTwigTemplateAsBody()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $subject = 'This is a test email from PapaLocal.';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';
        $templateName = 'emails/fakeTemplateName.html.twig';
        $templateArgs = array(
            'arg1' => 'value1'
        );

        $this->twigEnvMock->expects($this->once())
            ->method('render')
            ->with($templateName, $templateArgs)
            ->willReturn($message);

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->subject($subject)
                                ->usingTwigTemplate($templateName, $templateArgs)
                                ->build();

        // make assertions
        $this->assertInstanceOf(EmailMessageInterface::class, $emailMessage, 'unexpected type');
        $this->assertArraySubset($emailMessage->getRecipients(), array($recipient), 'unexpected recipients');
        $this->assertSame($emailMessage->getSubject(), $subject, 'unexpected subject');
        $this->assertSame($emailMessage->getBody(), $message, 'unexpected body');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(At least one recipient must be specified to build the email message)/
     */
    public function testBuildThrowsExceptionWhenNoRecipientsSpecified()
    {
        // set up fixtures
        $subject = 'This is a test email from PapaLocal.';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->subject($subject)
                                ->message($message)
                                ->build();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(A subject must be specified to build the email message)/
     */
    public function testBuildThrowsExceptionWhenSubjectNotSpecified()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->message($message)
                                ->build();

    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(A message body must be specified to build the email message)/
     */
    public function testBuildThrowsExceptionWhenNoBodySpecified()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $subject = 'This is a test email from PapaLocal.';

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->subject($subject)
                                ->build();

    }

    /**
     * @expectedException \BadFunctionCallException
     * @expectedExceptionMessageRegExp /^(Cannot call a function that sets the message body more than once per build)/
     */
    public function testUsingTwigTemplateThrowsExceptionWhenBodyAlreadySet()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $subject = 'This is a test email from PapaLocal.';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';
        $templateName = 'emails/fakeTemplateName.html.twig';
        $templateArgs = array(
            'arg1' => 'value1'
        );

        $this->twigEnvMock->expects($this->once())
                          ->method('render')
                          ->with($templateName, $templateArgs)
                          ->willReturn($message);

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->subject($subject)
                                ->usingTwigTemplate($templateName, $templateArgs)
                                ->usingTwigTemplate($templateName, $templateArgs)   // second call triggers exception
                                ->build();
    }

    /**
     * @expectedException \BadFunctionCallException
     * @expectedExceptionMessageRegExp /^(Cannot call a function that sets the message body more than once per build)/
     */
    public function testMessageThrowsExceptionWhenBodyAlreadySet()
    {
        // set up fixtures
        $recipient = 'test@papalocal.com';
        $subject = 'This is a test email from PapaLocal.';
        $message = '<p>This the a test message. You do not need to respond, and can delete this email.</p>';

        $builder = new EmailMessageBuilder($this->twigEnvMock);

        // exercise SUT
        $emailMessage = $builder->sendTo($recipient)
                                ->subject($subject)
                                ->message($message)
                                ->message('Second message will trigger exception.')
                                ->build();

    }
}