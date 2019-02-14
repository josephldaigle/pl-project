<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 9:38 AM
 */

namespace Test\Functional\Application;


use PapaLocal\Data\Ewebify;
use PapaLocal\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;


/**
 * EmailTest.
 *
 * Tests that the application can send e-mails.
 *
 * Does not test that the email is received. Only that it was sent.
 * Delivery is disabled for SwiftMailer in config_test.yml.
 */
class EmailTest extends WebTestCase
{
    /**
     * @var string the email address all test emails go to by default.
     */
    private $emailRecipient = Ewebify::ADMIN_EMAIL;

    /**
     * Test that the application can successfully send email.
     */
    public function testCanSendEmail()
    {
        // set up fixtures


        // create a url
        $url = 'http://www.google.com';

        // fetch templating engine
        $twig = $this->diContainer->get('templating');

        // create an email object
        $message = (new \Swift_Message('Password reset link for ' . ucfirst(Ewebify::APP_NAME)))
            ->setFrom(Ewebify::ADMIN_EMAIL)
            ->setTo($this->emailRecipient)
            ->setBody(
                $twig->render(
                    'emails/account/newUser.html.twig',
                    array('url' => $url)
                ),
                'text/html'
            );

        // fetch mailer from container
        $mailer = $this->diContainer->get('mailer');

        //exercise SUT
        $numSuccesses = $mailer->send($message);

        //make assertions
        $this->assertSame(1, $numSuccesses, 'email failed to send');
    }
}