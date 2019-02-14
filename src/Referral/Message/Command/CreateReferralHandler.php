<?php
/**
 * Created by PhpStorm.
 * Date: 10/12/18
 * Time: 7:53 AM
 */

namespace PapaLocal\Referral\Message\Command;


use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ReferralService;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class CreateReferralHandler
 */
class CreateReferralHandler
{
    /**
     * @var ReferralService
     */
    private $referralService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * CreateReferralHandler constructor.
     * @param ReferralService $referralService
     * @param SerializerInterface $serializer
     */
    public function __construct(ReferralService $referralService, SerializerInterface $serializer)
    {
        $this->referralService = $referralService;
        $this->serializer = $serializer;
    }

    /**
     * @param CreateReferral $command
     */
    public function __invoke(CreateReferral $command)
    {
        // Convert form into referral
        $referral = $this->serializer->denormalize(array(
            'providerUserGuid' => $command->getProviderGuid(),
            'currentPlace' => 'initialized',
            'firstName' => $command->getForm()->getFirstName(),
            'lastName' => $command->getForm()->getLastName(),
            'phoneNumber' => $command->getForm()->getPhoneNumber(),
            'emailAddress' => $command->getForm()->getEmailAddress(),
            'address' => $command->getForm()->getAddress(),
            'about' => $command->getForm()->getAbout(),
            'note' => $command->getForm()->getNote(),
            'recipient' => $command->getForm()->getRecipient(),
        ), Referral::class, 'array');

        // Pass referral to service
        $this->referralService->createReferral($referral);

        return;
    }
}