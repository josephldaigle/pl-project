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
use PapaLocal\Referral\ValueObject\ContactRecipient;


/**
 * Class ReferralInvitationConfirmation
 * @package PapaLocal\Referral\Notification\Message
 */
class ReferralInvitationConfirmation extends AbstractNotification implements EmailStrategyInterface
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
     * ReferralInvitationConfirmation constructor.
     * @param ContactRecipient $recipient
     * @param User $provider
     */
    public function __construct(ContactRecipient $recipient, User $provider)
    {
        $this->providerEmailAddress = $provider->getUsername();
        $this->templateArgs = array('recipient' => $recipient, 'provider' => $provider);

        $this->title = sprintf('You have successfully sent a referral to %s %s.', $recipient->getFirstName(), $recipient->getLastName());
        $this->messageTemplate = 'Once %s registers and creates an agreement in PapaLocal, you will get paid for each additional referral you send to their agreement(s).';
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
        return 'emails/referral/referralInvitationConfirmation.html.twig';
    }

    /**
     * @return array
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }
}