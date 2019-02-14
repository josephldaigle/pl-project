<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\InviteeService;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SaveAgreementInviteeHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class SaveAgreementInviteeHandler
{
    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SaveAgreementInviteeHandler constructor.
     *
     * @param InviteeService      $inviteeService
     * @param SerializerInterface $serializer
     */
    public function __construct(InviteeService $inviteeService,
                                SerializerInterface $serializer)
    {
        $this->inviteeService = $inviteeService;
        $this->serializer     = $serializer;
    }

    /**
     * @param SaveAgreementInvitee $command
     *
     * @throws \Exception
     */
    function __invoke(SaveAgreementInvitee $command)
    {
        // convert request into entity
        $invitee = $this->serializer->denormalize(array(
            'guid' => array('value' => $command->getInviteeGuid()->value()),
            'agreementId' => array('value' => $command->getAgreementGuid()->value()),
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName(),
            'message' => $command->getMessage(),
            'emailAddress' => array('emailAddress' => $command->getEmailAddress()->getEmailAddress(), 'type' => array('value' => $command->getEmailAddress()->getType()->getValue()))
        ), ReferralAgreementInvitee::class, 'array');

        if ($command->getPhoneNumber() instanceof PhoneNumber)
        {
            $invitee->setPhoneNumber($command->getPhoneNumber());
        }

        // invoke service call
        $this->inviteeService->saveInvitee($invitee);

        return;
    }

}