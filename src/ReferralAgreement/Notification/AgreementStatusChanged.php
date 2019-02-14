<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/11/18
 * Time: 12:41 PM
 */

namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Notification\AbstractNotification;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;


/**
 * Class AgreementStatusChanged
 *
 * An application notification that is sent to agreement participants whenever the agreement's status  changes.
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class AgreementStatusChanged extends AbstractNotification
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * AgreementStatusChanged constructor.
     *
     * @param                 $recipient
     * @param string          $agreementName
     * @param AgreementStatus $status
     */
    public function __construct($recipient, string $agreementName, AgreementStatus $status)
    {
        $this->title = sprintf('The status of an agreement you\'re providing referrals to has changed to %s.', $status->getStatus()->getValue());

        $this->messageTemplate = 'The agreement %s\'s status has changed to %s.';

        $this->messageBodyArgs = array($agreementName, $status->getStatus()->getValue());

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