<?php
/**
 * Created by PhpStorm.
 * Date: 9/25/18
 * Time: 8:59 AM
 */

namespace PapaLocal\Referral\Data;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Referral\Entity\Factory\ReferralFactory;
use PapaLocal\Referral\Entity\Referral;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class ReferralRepository
 * @package PapaLocal\Referral\Data
 */
class ReferralRepository
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var ReferralFactory
     */
    private $referralFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * ReferralRepository constructor.
     * @param MessageFactory $messageFactory
     * @param ReferralFactory $referralFactory
     * @param MessageBusInterface $mysqlBus
     */
    public function __construct(MessageFactory $messageFactory, ReferralFactory $referralFactory, MessageBusInterface $mysqlBus)
    {
        $this->messageFactory = $messageFactory;
        $this->referralFactory = $referralFactory;
        $this->mysqlBus = $mysqlBus;
    }

    /**
     * @param Guid $guid
     * @return Referral
     */
    public function fetchByGuid(Guid $guid)
    {
        $query = $this->messageFactory->newFindByGuid('v_referral', $guid);
        $record = $this->mysqlBus->dispatch($query);

        $referral = $this->referralFactory->fromRecord($record);

        return $referral;
    }

    /**
     * @param EmailAddress $emailAddress
     * @return mixed
     */
    public function fetchByRecipientEmailAddress(EmailAddress $emailAddress)
    {
        $query = $this->messageFactory->newFindBy('v_referral', 'recipientEmailAddress', $emailAddress->getEmailAddress());
        $recordSet = $this->mysqlBus->dispatch($query);

        $referralCollection = $this->referralFactory->fromRecordSet($recordSet);

        return $referralCollection;
    }

    /**
     * Find referrals sent to agreements where $userGuid is the owner of the agreement.
     *
     * @param Guid $agreementOwnerGuid
     *
     * @return mixed
     */
    public function fetchByAgreementOwnerGuid(Guid $agreementOwnerGuid)
    {
        $query = $this->messageFactory->newFindBy('v_referral', 'agreementOwnerGuid', $agreementOwnerGuid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        $referralCollection = $this->referralFactory->fromRecordSet($recordSet);

        return $referralCollection;
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return mixed
     */
    public function fetchByAgreementGuid(GuidInterface $agreementGuid)
    {
        $query = $this->messageFactory->newFindBy('v_referral', 'agreementGuid', $agreementGuid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        $referralCollection = $this->referralFactory->fromRecordSet($recordSet);

        return $referralCollection;
    }

    /**
     * @param Guid $guid
     * @return mixed
     */
    public function fetchByContactGuid(Guid $guid)
    {
        $query = $this->messageFactory->newFindBy('v_referral', 'contactGuid', $guid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        $referralCollection = $this->referralFactory->fromRecordSet($recordSet);

        return $referralCollection;
    }

    /**
     * @param Guid $guid
     * @return mixed
     */
    public function fetchByProviderGuid(Guid $guid)
    {
        $query = $this->messageFactory->newFindBy('v_referral', 'providerUserGuid', $guid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        $referralCollection = $this->referralFactory->fromRecordSet($recordSet);

        return $referralCollection;
    }
}