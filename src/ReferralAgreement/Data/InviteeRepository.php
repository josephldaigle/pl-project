<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 7/12/18
 * Time: 8:44 PM
 */


namespace PapaLocal\ReferralAgreement\Data;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Entity\Factory\InviteeFactory;
use PapaLocal\Entity\Collection\Collection;
use Symfony\Component\Messenger\MessageBusInterface;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;


/**
 * Class InviteeRepository.
 *
 * @package PapaLocal\ReferralAgreement\Data
 */
class InviteeRepository
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var InviteeFactory
     */
    private $inviteeFactory;

    /**
     * InviteeRepository constructor.
     *
     * @param MessageBusInterface   $mysqlBus
     * @param MessageFactory        $mysqlMsgFactory
     * @param InviteeFactory        $inviteeFactory
     */
    public function __construct(
        MessageBusInterface $mysqlBus,
        MessageFactory $mysqlMsgFactory,
        InviteeFactory $inviteeFactory
    )
    {
        $this->mysqlBus = $mysqlBus;
        $this->mysqlMsgFactory = $mysqlMsgFactory;
        $this->inviteeFactory = $inviteeFactory;
    }

    /**
     * @param GuidInterface $inviteeGuid
     *
     * @return ReferralAgreementInvitee
     */
    public function findByGuid(GuidInterface $inviteeGuid)
    {
        $query = $this->mysqlMsgFactory->newFindByGuid('v_referral_agreement_invitee', $inviteeGuid);
        $record = $this->mysqlBus->dispatch($query);

        $invitee = $this->inviteeFactory->createFromRecord($record);

        return $invitee;
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return Collection
     */
    public function findAllByAgreementGuid(GuidInterface $agreementGuid): Collection
    {
        $query = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_invitee', 'agreementGuid', $agreementGuid->value());
        $records = $this->mysqlBus->dispatch($query);

        $inviteeList = $this->inviteeFactory->createFromRecordSet($records);

        return $inviteeList;
    }

    /**
     * @param EmailAddress $emailAddress
     *
     * @return Collection
     */
    public function findAllByEmailAddress(EmailAddress $emailAddress)
    {
        $query = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_invitee', 'emailAddress', $emailAddress->getEmailAddress());
        $records = $this->mysqlBus->dispatch($query);

        $inviteeList = $this->inviteeFactory->createFromRecordSet($records);

        return $inviteeList;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return Collection
     */
    public function findAllByUserGuid(GuidInterface $userGuid)
    {
        $query = $this->mysqlMsgFactory->newFindBy('v_referral_agreement_invitee', 'userGuid', $userGuid->value());
        $records = $this->mysqlBus->dispatch($query);

        $inviteeList = $this->inviteeFactory->createFromRecordSet($records);

        return $inviteeList;
    }

    /**
     * @param array $cols
     *
     * @return Collection
     */
    public function findByCols(array $cols)
    {
        $query = $this->mysqlMsgFactory->newFindByCols('v_referral_agreement_invitee', $cols);
        $records = $this->mysqlBus->dispatch($query);

        $inviteeList = $this->inviteeFactory->createFromRecordSet($records);

        return $inviteeList;
    }
}