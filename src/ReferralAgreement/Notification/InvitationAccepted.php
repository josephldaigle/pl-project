<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 5:37 PM
 */

namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\ValueObject\Strategy\Strategy;


/**
 * Class AgreementAccepted
 *
 * Sent to users when an invitee joins an agreement.
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class InvitationAccepted extends AbstractNotification
{
    /**
     * AgreementAccepted constructor.
     *
     * @param string $inviteeFirstName
     * @param string $inviteeLastName
     */
    public function __construct(string $inviteeFirstName, string $inviteeLastName)
    {
        $this->title = sprintf('%s %s has accepted your invitation to sell referrals.', $inviteeFirstName, $inviteeLastName);
        $this->messageTemplate = '%s has accepted your invitation to sell referrals. You can see their invitation status updated in the Participants section of your agreement.';

        $this->messageBodyArgs = array(
            $inviteeFirstName
        );
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    protected function getConfiguredStrategies(): array
    {
        return [Strategy::APP()->value()];
    }

}