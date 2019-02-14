<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/30/19
 * Time: 6:38 PM
 */


namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Notification\AbstractNotification;


/**
 * AgreementNameChange.
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class AgreementNameChange extends AbstractNotification
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * AgreementNameChange constructor.
     *
     * @param string $recipient
     */
    public function __construct($recipient, string $oldName, string $newName)
    {
        $this->title = 'Agreement Name Changed';
        $this->messageTemplate = 'The referral agreement named %s has had it\'s name changed to %s.';
        $this->messageBodyArgs = [$oldName, $newName];
        $this->recipient = $recipient;
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return [self::STRATEGY_APP];
    }
}