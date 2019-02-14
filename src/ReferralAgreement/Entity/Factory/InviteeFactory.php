<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/2/18
 */

namespace PapaLocal\ReferralAgreement\Entity\Factory;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class InviteeFactory.
 *
 * @package PapaLocal\ReferralAgreement\Entity\Factory
 */
class InviteeFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * InviteeFactory constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RecordInterface $record
     *
     * @return ReferralAgreementInvitee
     */
    public function createFromRecord(RecordInterface $record): ReferralAgreementInvitee
    {
        $emailAddress = $this->serializer->denormalize(array(
            'emailAddress' => $record['emailAddress'],
            'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
        ), EmailAddress::class, 'array');

        $inviteeGuid = $this->serializer->denormalize(array('value' => $record['guid']), Guid::class, 'array');
        $agreementId = $this->serializer->denormalize(array('value' => $record['agreementGuid']), Guid::class, 'array');

        $invitee = new ReferralAgreementInvitee($inviteeGuid, $agreementId, $record['firstName'], $record['lastName'], $record['message'], $emailAddress);

        if (! is_null($record['phoneNumber'])) {
            $phoneNumber = $this->serializer->denormalize(array(
                'phoneNumber' => $record['phoneNumber'],
                'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
            ), PhoneNumber::class, 'array');
            $invitee->setPhoneNumber($phoneNumber);
        }

        if ($record['userGuid'] !== '') {
            $userId = $this->serializer->denormalize(array('value' => $record['userGuid']), Guid::class, 'array');
            $invitee->setUserId($userId);
        }

        $invitee->setIsDeclined($record['declined']);
        $invitee->setIsParticipant($record['isParticipant']);

        // set current status
        if (isset($record['timeSent']) && ! is_null($record['timeSent'])) {
            $invitee->setTimeNotified($record['timeSent']);

            if ($record['removed']) {
                $invitee->setCurrentPlace('Removed');
            }elseif ($record['declined']) {
                $invitee->setCurrentPlace('Declined');
            } elseif ($record['isParticipant']) {
                $invitee->setCurrentPlace('Accepted');
            } else {
                $invitee->setCurrentPlace('Invited');
            }
        } else {
            $invitee->setCurrentPlace('Created');
        }

        return $invitee;
    }

    /**
     * @param RecordSetInterface $recordSet
     *
     * @return Collection
     */
    public function createFromRecordSet(RecordSetInterface $recordSet): Collection
    {
        $list = $this->serializer->denormalize(array(), Collection::class, 'array');

        foreach ($recordSet as $record)
        {
            $invitee = $this->createFromRecord($record);
            $list->add($invitee);
        }

        return $list;
    }
}