<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 9:13 PM
 */


namespace PapaLocal\ReferralAgreement\Message;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\RenewStrategy;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateQuantity;
use PapaLocal\ReferralAgreement\Form\CreateAgreementForm;
use PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\ActivateAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\CreateReferralAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PauseAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateStrategy;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\AcceptInvitation;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\DeclineInvitation;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PublishAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\SaveAgreementInvitee;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateName;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadParticipantAgreements;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadUserAgreements;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByAgreementGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByUserGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByEmailAddress;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;


/**
 * Class MessageFactory
 *
 * @package PapaLocal\ReferralAgreement\Message
 */
class MessageFactory
{
    /********************
     * COMMANDS
     ********************/

    /**
     * @param GuidInterface       $agreementId
     * @param CreateAgreementForm $createAgreementForm
     * @param GuidInterface       $userId
     * @param GuidInterface       $companyGuid
     *
     * @return CreateReferralAgreement
     */
    public function newCreateReferralAgreement(
        GuidInterface $agreementId,
        CreateAgreementForm $createAgreementForm,
        GuidInterface $userId,
        GuidInterface $companyGuid
    ): CreateReferralAgreement
    {
        return new CreateReferralAgreement($agreementId, $createAgreementForm, $userId, $companyGuid);
    }

    /**
     * @param GuidInterface $agreementId
     *
     * @return PublishAgreement
     */
    public function newPublishAgreement(GuidInterface $agreementId): PublishAgreement
    {
        return new PublishAgreement($agreementId);
    }

    /**
     * @param GuidInterface      $agreementGuid
     * @param StatusChangeReason $changeReason
     * @param GuidInterface      $requestorGuid
     *
     * @return PauseAgreement
     */
    public function newPauseAgreement(GuidInterface $agreementGuid, StatusChangeReason $changeReason, GuidInterface $requestorGuid): PauseAgreement
    {
        return new PauseAgreement($agreementGuid, $changeReason, $requestorGuid);
    }

    /**
     * @param string $agreementGuid
     * @param string $changeReason
     * @param string $requestorGuid
     *
     * @return ActivateAgreement
     */
    public function newActivateAgreement(string $agreementGuid, string $changeReason, string $requestorGuid): ActivateAgreement
    {
        return new ActivateAgreement($agreementGuid, $changeReason, $requestorGuid);
    }

    /**
     * @param GuidInterface   $agreementId
     * @param string $name
     *
     * @return UpdateName
     */
    public function newUpdateName(GuidInterface $agreementId, string $name): UpdateName
    {
        return new UpdateName($agreementId, $name);
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
     * @param string $agreementGuid
     * @param int    $quantity
     *
     * @return UpdateQuantity
     */
    public function newUpdateAgreementQuantity(string $agreementGuid, int $quantity): UpdateQuantity
    {
        return new UpdateQuantity($agreementGuid, $quantity);
    }

    /**
     * @param string $agreementGuid
     * @param string $strategy
     *
     * @return UpdateStrategy
     */
    public function newUpdateAgreementStrategy(string $agreementGuid, string $strategy): UpdateStrategy
    {
        return new UpdateStrategy($agreementGuid, $strategy);
    }

    /**
     * @param GuidInterface                $inviteeGuid
     * @param ReferralAgreementInviteeForm $inviteeForm
     *
     * @return SaveAgreementInvitee
     */
    public function newSaveAgreementInvitee(GuidInterface $inviteeGuid, ReferralAgreementInviteeForm $inviteeForm): SaveAgreementInvitee
    {
        return new SaveAgreementInvitee($inviteeGuid, $inviteeForm);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $userGuid
     *
     * @return AcceptInvitation
     */
    public function newAcceptInvitation(GuidInterface $agreementGuid, GuidInterface $userGuid): AcceptInvitation
    {
        return new AcceptInvitation($agreementGuid, $userGuid);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $userGuid
     *
     * @return DeclineInvitation
     */
    public function newDeclineInvitation(GuidInterface $agreementGuid, GuidInterface $userGuid): DeclineInvitation
    {
        return new DeclineInvitation($agreementGuid, $userGuid);
    }

    /**
     * @return RenewStrategy
     */
    public function newRenewStrategy()
    {
        return new RenewStrategy();
    }

    /********************
     * QUERIES
     ********************/

    /**
     * @param GuidInterface $guid
     *
     * @return FindByGuid
     */
    public function newFindAgreementByGuid(GuidInterface $guid): FindByGuid
    {
        return new FindByGuid($guid);
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return LoadUserAgreements
     */
    public function newLoadUserAgreements(GuidInterface $userGuid): LoadUserAgreements
    {
        return new LoadUserAgreements($userGuid);
    }

    /**
     * @param GuidInterface $participantGuid
     *
     * @return LoadParticipantAgreements
     */
    public function newLoadParticipantAgreements(GuidInterface $participantGuid): LoadParticipantAgreements
    {
        return new LoadParticipantAgreements($participantGuid);
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return FindByAgreementGuid
     */
    public function newFindInvitationsByAgreementGuid(GuidInterface $agreementGuid): FindByAgreementGuid
    {
        return new FindByAgreementGuid($agreementGuid);
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return FindByUserGuid
     */
    public function newFindInvitationsByUserGuid(GuidInterface $userGuid): FindByUserGuid
    {
        return new FindByUserGuid($userGuid);
    }

    /**
     * @param EmailAddress $emailAddress
     *
     * @return FindByEmailAddress
     */
    public function newFindInvitationsByEmailAddress(EmailAddress $emailAddress): FindByEmailAddress
    {
        return new FindByEmailAddress($emailAddress);
    }
}