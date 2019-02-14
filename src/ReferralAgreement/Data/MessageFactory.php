<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 6:14 PM
 */


namespace PapaLocal\ReferralAgreement\Data;


use PapaLocal\Core\Data\AbstractMessageFactory;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\SaveAgreement;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateLocations;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateQuantity;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateReferralPrice;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateServices;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateStrategy;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\AcceptInvitation;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\AssignUserGuid;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\DeclineInvitation;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\MarkInvitationSent;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\RemoveInvitee;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInvitee;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementName;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementStatus;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;


/**
 * Class MessageFactory
 *
 * Factory for creating query messages to messenger.bus.mysql.
 *
 * @package PapaLocal\ReferralAgreement\Data
 */
class MessageFactory extends AbstractMessageFactory
{
    /***********************
     *  COMMANDS
     **********************/

    /**
     * @param ReferralAgreement $referralAgreement
     *
     * @return SaveAgreement
     */
    public function newSaveAgreement(ReferralAgreement $referralAgreement): SaveAgreement
    {
        return new SaveAgreement($referralAgreement);
    }

    /**
     * @param GuidInterface $agreementId
     * @param string        $name
     *
     * @return UpdateAgreementName
     */
    public function newUpdateAgreementName(GuidInterface $agreementId, string $name): UpdateAgreementName
    {
        return new UpdateAgreementName($agreementId, $name);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param string        $description
     *
     * @return UpdateDescription
     */
    public function newUpdateAgreementDescription(GuidInterface $agreementGuid, string $description): UpdateDescription
    {
        return new UpdateDescription($agreementGuid, $description);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param int           $quantity
     *
     * @return UpdateQuantity
     */
    public function newUpdateAgreementQuantity(GuidInterface $agreementGuid, int $quantity): UpdateQuantity
    {
        return new UpdateQuantity($agreementGuid, $quantity);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param string        $strategy
     *
     * @return UpdateStrategy
     */
    public function newUpdateAgreementStrategy(GuidInterface $agreementGuid, string $strategy): UpdateStrategy
    {
        return new UpdateStrategy($agreementGuid, $strategy);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param float         $price
     *
     * @return UpdateReferralPrice
     */
    public function newUpdateReferralPrice(GuidInterface $agreementGuid, float $price): UpdateReferralPrice
    {
        return new UpdateReferralPrice($agreementGuid, $price);
    }

    /**
     * @param GuidInterface      $agreementGuid
     * @param IncludeExcludeList $locations
     *
     * @return UpdateLocations
     */
    public function newUpdateLocations(GuidInterface $agreementGuid, IncludeExcludeList $locations): UpdateLocations
    {
        return new UpdateLocations($agreementGuid, $locations);
    }

    /**
     * @param GuidInterface      $agreementGuid
     * @param IncludeExcludeList $services
     *
     * @return UpdateServices
     */
    public function newUpdateServices(GuidInterface $agreementGuid, IncludeExcludeList $services): UpdateServices
    {
        return new UpdateServices($agreementGuid, $services);
    }

    /**
     * @param AgreementStatus $status
     *
     * @return UpdateAgreementStatus
     */
    public function newUpdateAgreementStatus(AgreementStatus $status): UpdateAgreementStatus
    {
        return new UpdateAgreementStatus($status);
    }

    /**
     * @param ReferralAgreementInvitee $referralAgreementInvitee
     *
     * @return SaveInvitee
     */
    public function newSaveInvitee(ReferralAgreementInvitee $referralAgreementInvitee): SaveInvitee
    {
        return new SaveInvitee($referralAgreementInvitee);
    }

    /**
     * @param string $agreementGuid
     * @param string $inviteeEmailAddress
     *
     * @return AcceptInvitation
     */
    public function newAcceptInvitation(string $agreementGuid, string $inviteeEmailAddress): AcceptInvitation
    {
        return new AcceptInvitation($agreementGuid, $inviteeEmailAddress);
    }

    /**
     * @param GuidInterface $invitationGuid
     *
     * @return DeclineInvitation
     */
    public function newDeclineInvitation(GuidInterface $invitationGuid): DeclineInvitation
    {
        return new DeclineInvitation($invitationGuid);
    }

    /**
     * @param GuidInterface $invitationGuid
     *
     * @return MarkInvitationSent
     */
    public function newMarkInvitationSent(GuidInterface $invitationGuid): MarkInvitationSent
    {
        return new MarkInvitationSent($invitationGuid);
    }

    /**
     * @param string $emailAddress
     * @param string $userGuid
     *
     * @return AssignUserGuid
     */
    public function newAssignUserGuidToInvitee(string $emailAddress, string $userGuid): AssignUserGuid
    {
        return new AssignUserGuid($emailAddress, $userGuid);
    }

    /**
     * @param GuidInterface $inviteeGuid
     *
     * @return RemoveInvitee
     */
    public function newRemoveInvitee(GuidInterface $inviteeGuid): RemoveInvitee
    {
        return new RemoveInvitee($inviteeGuid);
    }

    /***********************
     *  QUERIES
     **********************/
}