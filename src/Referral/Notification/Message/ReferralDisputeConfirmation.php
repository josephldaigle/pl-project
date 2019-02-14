<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/15/18
 * Time: 8:00 AM
 */

namespace PapaLocal\Referral\Notification\Message;


use PapaLocal\Entity\User;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ReferralDisputeConfirmation
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralDisputeConfirmation extends AbstractNotification implements EmailStrategyInterface
{
    /**
     * @var string
     */
    private $recipientEmailAddress;

    /**
     * @var array
     */
    private $templateArgs;

    /**
     * @var string
     */
    protected $title;

    /**
     * ReferralDisputeConfirmation constructor.
     * @param User $recipient
     * @param User $provider
     */
    public function __construct(User $recipient, User $provider)
    {
        $this->recipientEmailAddress = $recipient->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('You have successfully disputed %s %s\'s referral.', $provider->getFirstName(), $provider->getLastName());
        $this->messageTemplate = 'We are sorry to see that your were not satisfied with %s\'s referral. Our team will take a look at your case, and get back to you soon.';
        $this->messageBodyArgs = array($provider->getFirstName());
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return [self::STRATEGY_APP, self::STRATEGY_EMAIL];
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipientEmailAddress;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->title ;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return 'emails/referral/referralDisputeConfirmation.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}