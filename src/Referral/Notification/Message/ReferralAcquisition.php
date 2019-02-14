<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/12/18
 * Time: 12:03 PM
 */

namespace PapaLocal\Referral\Notification\Message;


use PapaLocal\Entity\User;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ReferralAcquisition
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralAcquisition extends AbstractNotification implements EmailStrategyInterface
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
     * ReferralAcquisition constructor.
     * @param User $recipient
     * @param User $provider
     */
    public function __construct(User $recipient, User $provider)
    {
        $this->recipientEmailAddress = $recipient->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('You have successfully acquired %s %s\'s referral(s).', $provider->getFirstName(), $provider->getLastName());
        $this->messageTemplate = 'Now you have the ability to go into the feed and rate %s\'s referral.';
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
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return 'emails/referral/referralAcquisition.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}