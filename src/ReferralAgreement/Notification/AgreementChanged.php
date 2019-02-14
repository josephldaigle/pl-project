<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/31/19
 */


namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Notification\AbstractNotification;


/**
 * Class AgreementChanged.
 *
 * This is a generic change notification that can be used to send a simple
 * message to agreement participants.
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class AgreementChanged extends AbstractNotification
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * AgreementChanged constructor.
     *
     * @param string $recipient
     * @param string $agreementName
     * @param string $field
     * @param string $oldValue
     * @param string $newValue
     */
    public function __construct(string $recipient, string $agreementName, string $field, string $oldValue, string $newValue)
    {

        $this->title = 'Agreement ' . ucfirst($field) . ' Changed';
        $this->messageTemplate = 'The referral agreement named %s has been changed. The %s was changed from %s to %s.';
        $this->messageBodyArgs = [$agreementName, $field, $oldValue, $newValue];

        $this->recipient = $recipient;

    }

    /**
     * @inheritDoc
     */
    protected function getConfiguredStrategies(): array
    {
        return [self::STRATEGY_APP];
    }
}