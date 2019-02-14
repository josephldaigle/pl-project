<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/23/18
 * Time: 12:45 PM
 */

namespace PapaLocal\Referral\Entity\Factory;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Referral\Entity\FeedItem;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\ValueObject\ReferralRating;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ReferralFactory
 * @package PapaLocal\Referral\Data
 */
class ReferralFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ReferralFactory constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function fromRecord(Record $record)
    {
        //TODO: replace new keywords with serializer

        $referral = $this->serializer->denormalize(array(
            'id' => $record['id'],
            'guid' => $this->serializer->denormalize(array('value' => $record['guid']), Guid::class, 'array'),
            'providerUserGuid' => $this->serializer->denormalize(array('value' => $record['providerUserGuid']), Guid::class, 'array'),
            'currentPlace' => $record['currentPlace'],
            'firstName' => $record['firstName'],
            'lastName' => $record['lastName'],
            'phoneNumber' => new PhoneNumber($record['phoneNumber'], PhoneNumberType::PERSONAL()),
            'emailAddress' => new EmailAddress($record['emailAddress'], EmailAddressType::PERSONAL()),
            'address' => $this->serializer->deserialize($record['address'], Address::class, 'json'),
            'about' => $record['about'],
            'note' => $record['note'],
            'timeCreated' => $record['timeCreated'],
            'timeUpdated' => $record['timeUpdated']
        ), Referral::class, 'array');

        if ((isset($record['contactGuid'])) && (! empty($record['contactGuid']))) {
            $referral->setRecipient(
                new ContactRecipient(
                    $record['recipientFirstName'],
                    $record['recipientLastName'],
                    new PhoneNumber($record['recipientPhoneNumber'], PhoneNumberType::PERSONAL()),
                    new EmailAddress($record['recipientEmailAddress'], EmailAddressType::USERNAME()),
                    $this->serializer->denormalize(array('value' => $record['contactGuid']), Guid::class, 'array')
                )
            );
        } else if ((isset($record['agreementGuid'])) && (! empty($record['agreementGuid']))) {
            $referral->setRecipient(
                new AgreementRecipient(
                    $this->serializer->denormalize(array('value' => $record['agreementGuid']), Guid::class, 'array')
                )
            );
        } else {
            $referral->setRecipient(new ContactRecipient(
                $record['recipientFirstName'],
                $record['recipientLastName'],
                new PhoneNumber($record['recipientPhoneNumber'], PhoneNumberType::PERSONAL()),
                new EmailAddress($record['recipientEmailAddress'], EmailAddressType::USERNAME())
            ));
        }

        if ((isset($record['score'])) && (! empty($record['score']))) {
            $rating = new ReferralRating(
                $record['score'],
                $record['feedback']
            );

            if ((isset($record['resolution'])) && (! empty($record['resolution']))) {
                $rating->setResolution($record['resolution']);
                $rating->setReviewerNote($record['reviewerNote']);
            }

            $referral->setRating($rating);
        }

        return $referral;
    }

    public function fromRecordSet(RecordSet $recordSet)
    {
        $referralCollection = $this->serializer->denormalize(array(), Collection::class, 'array');
        foreach ($recordSet as $record) {
            $referral = $this->fromRecord($record);
            $referralCollection->add($referral);
        }

        return $referralCollection;
    }
}