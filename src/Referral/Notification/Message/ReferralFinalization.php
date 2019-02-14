<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/12/18
 * Time: 12:04 PM
 */

namespace PapaLocal\Referral\Notification\Message;


use PapaLocal\Entity\User;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ReferralFinalization
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralFinalization extends AbstractNotification implements EmailStrategyInterface
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
     * ReferralFinalization constructor.
     * @param User $recipient
     * @param User $provider
     */
    public function __construct(User $recipient, User $provider)
    {
        $this->recipientEmailAddress = $recipient->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('You have successfully finalized %s %s\'s referral.', $provider->getFirstName(), $provider->getLastName());
        $this->messageTemplate = 'Congratulation, we are happy to see that you were satisfied with %s\'s referral. Your feedback will help us improve PapaLocal.';
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
        return 'emails/referral/referralFinalization.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}