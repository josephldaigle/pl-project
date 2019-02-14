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
 * Class ReferralDispute
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralDispute extends AbstractNotification implements EmailStrategyInterface
{
    /**
     * @var string
     */
    private $providerEmailAddress;

    /**
     * @var array
     */
    private $templateArgs;

    /**
     * @var string
     */
    protected $title;

    /**
     * ReferralDispute constructor.
     * @param User $recipient
     * @param User $provider
     */
    public function __construct(User $recipient, User $provider)
    {
        $this->providerEmailAddress = $recipient->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('%s %s has disputed your referral.', $recipient->getFirstName(), $recipient->getLastName());
        $this->messageTemplate = 'We are sorry to see that %s disputed your referral. Our team will take a look at your case, and get back to you soon.';
        $this->messageBodyArgs = array($recipient->getFirstName());
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
        return $this->providerEmailAddress;
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
        return 'emails/referral/referralDispute.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}