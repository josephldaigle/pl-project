<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/23/18
 * Time: 3:54 PM
 */

namespace PapaLocal\Referral\Data\Command;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Core\Exception\InvalidStateException;
use Symfony\Component\Serializer\Serializer;


/**
 * Class UpdateReferralHandler
 * @package PapaLocal\Referral\Data\Command
 */
class UpdateReferralHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * UpdateReferralHandler constructor.
     * @param TableGatewayInterface $tableGateway
     * @param Serializer $serializer
     */
    public function __construct(TableGatewayInterface $tableGateway, Serializer $serializer)
    {
        $this->tableGateway = $tableGateway;
        $this->serializer = $serializer;
    }

    public function __invoke(UpdateReferral $command)
    {
        $this->tableGateway->setTable('Referral');
        $referral = $this->tableGateway->findByGuid($command->getGuid());

        $referral['id'] = $command->getId();
        $referral['guid'] = $command->getGuid();
        $referral['providerUserGuid'] = $command->getProviderUserGuid();
        $referral['currentPlace'] = $command->getCurrentPlace();
        $referral['firstName'] = $command->getFirstName();
        $referral['lastName'] = $command->getLastName();
        $referral['phoneNumber'] = $command->getPhoneNumber();
        $referral['emailAddress'] = $command->getEmailAddress();
        $referral['address'] = $command->getAddress();
        $referral['about'] = $command->getAbout();
        $referral['note'] = $command->getNote();

        if ($command->isContactRecipient()){
            $referral['contactGuid'] = $command->getContactGuid();
            $referral['recipientFirstName'] = $command->getRecipientFirstName();
            $referral['recipientLastName'] = $command->getRecipientLastName();
            $referral['recipientPhoneNumber'] = $command->getRecipientPhoneNumber();
            $referral['recipientEmailAddress'] = $command->getRecipientEmailAddress();
        } else {
            $referral['agreementGuid'] = $command->getAgreementGuid();
        }

        if ($command->isRated()) {
            $referral['score'] = $command->getScore();
            $referral['feedback'] = $command->getRatingNote();
        }

        if ($command->isDisputed()) {
            $referral['reviewerNote'] = $command->getReviewerNote();
            $referral['reviewerGuid'] = $command->getReviewerGuid();
            $referral['resolution'] = $command->getResolution();
        }

        $this->tableGateway->update($referral->properties());

        return;
    }
}