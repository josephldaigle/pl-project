<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 12:30 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Query\Invitee;


use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class FindAllHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Query\Invitee
 */
class FindAllHandler
{
    /**
     * @var TableGateway
     */
    private $tableGateway;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * FindAllHandler constructor.
     *
     * @param TableGateway        $tableGateway
     * @param SerializerInterface $serializer
     */
    public function __construct(TableGateway $tableGateway, SerializerInterface $serializer)
    {
        $this->tableGateway = $tableGateway;
        $this->serializer   = $serializer;
    }

    /**
     * @param FindAll $query
     *
     * @return array
     */
    public function __invoke(FindAll $query)
    {
        $this->tableGateway->setTable('v_referral_agreement_invitee');

        $rows = $this->tableGateway->findAllOrderedById();

        // convert qry results into Collection of Invitees
        $inviteeList = $this->serializer->denormalize(array(), Collection::class, 'array');

        foreach ($rows as $row) {

            $emailAddress = $this->serializer->denormalize(array(
                'emailAddress' => $row['emailAddress'],
                'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
            ), EmailAddress::class, 'array');

            $agreementId = $this->serializer->denormalize(array('value' => $row['agreementGuid']), Guid::class, 'array');

            $invitee = new ReferralAgreementInvitee($agreementId, $row['firstName'], $row['lastName'], $row['message'], $emailAddress);

            if (!is_null($row['phoneNumber'])) {
                $phoneNumber = $this->serializer->denormalize(array(
                    'phoneNumber' => $row['phoneNumber'],
                    'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
                ), PhoneNumber::class, 'array');
                $invitee->setPhoneNumber($phoneNumber);
            }

            if ($row['userGuid'] !== '') {
                $userId = $this->serializer->denormalize(array('value' => $row['userGuid']), Guid::class, 'array');
                $invitee->setUserId($userId);
            }

            $invitee->setIsDeclined($row['declined']);
            $invitee->setIsParticipant($row['isParticipant']);

            if (! is_null($row['timeSent'])) {
                $invitee->setTimeNotified($row['timeSent']);

                if ($row['declined']) {
                    $invitee->setCurrentPlace('Declined');
                } elseif ($row['isParticipant']) {
                    $invitee->setCurrentPlace('Accepted');
                } else {
                    $invitee->setCurrentPlace('Invited');
                }
            } else {
                $invitee->setCurrentPlace('Created');
            }

            $inviteeList->add($invitee);
        }

        return $inviteeList;
    }
}