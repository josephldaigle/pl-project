<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/17/18
 * Time: 2:59 PM
 */

namespace PapaLocal\Referral\Data\Command;


use PapaLocal\Core\Data\TableGatewayInterface;
use Symfony\Component\Serializer\Serializer;


/**
 * Class SaveReferralHandler
 * @package PapaLocal\Referral\Data\Command
 */
class SaveReferralHandler
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
     * SaveReferralHandler constructor.
     * @param TableGatewayInterface $tableGateway
     * @param Serializer $serializer
     */
    public function __construct(TableGatewayInterface $tableGateway, Serializer $serializer)
    {
        $this->tableGateway = $tableGateway;
        $this->serializer = $serializer;
    }

    public function __invoke(SaveReferral $command)
    {
        $row = array(
            'guid' => $command->getGuid(),
            'providerUserGuid' => $command->getProviderUserGuid(),
            'currentPlace' => $command->getCurrentPlace(),
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName(),
            'phoneNumber' => $command->getPhoneNumber(),
            'emailAddress' => $command->getEmailAddress(),
            'address' => $this->serializer->serialize($command->getAddress(), 'json'),
            'about' => $command->getAbout(),
            'note' => $command->getNote(),
        );

        if ($command->isContactRecipient()){
            $row['recipientFirstName'] = $command->getRecipientFirstName();
            $row['recipientLastName'] = $command->getRecipientLastName();
            $row['recipientPhoneNumber'] = $command->getRecipientPhoneNumber();
            $row['recipientEmailAddress'] = $command->getRecipientEmailAddress();
        } else {
            $row['agreementGuid'] = $command->getAgreementGuid();
        }

        $this->tableGateway->setTable('Referral');
        $this->tableGateway->create($row);

        return;
    }
}