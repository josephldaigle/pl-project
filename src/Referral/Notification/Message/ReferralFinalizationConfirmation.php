<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/15/18
 * Time: 8:02 AM
 */

namespace PapaLocal\Referral\Notification\Message;


use PapaLocal\Entity\User;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ReferralFinalizationConfirmation
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralFinalizationConfirmation extends AbstractNotification implements EmailStrategyInterface
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
     * ReferralFinalizationConfirmation constructor.
     * @param User $recipient
     * @param User $provider
     */
    public function __construct(User $recipient, User $provider)
    {
        $this->providerEmailAddress = $recipient->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('%s has successfully finalized your referral.', $recipient->getFirstName());
        $this->messageTemplate = 'Congratulation, %s appreciated your referral. You will be able to checkout your funds during the next cash out window.';
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
        return 'emails/referral/referralFinalizationConfirmation.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}