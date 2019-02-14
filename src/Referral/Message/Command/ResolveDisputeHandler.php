<?php
/**
 * Created by PhpStorm.
 * Date: 10/12/18
 * Time: 7:53 AM
 */

namespace PapaLocal\Referral\Message\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\ReferralService;
use PapaLocal\Referral\ValueObject\ReferralRating;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class ResolveDisputeHandler
 * @package PapaLocal\Referral\Message\Command
 */
class ResolveDisputeHandler
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
     * ResolveDisputeHandler constructor.
     * @param ReferralService $referralService
     * @param SerializerInterface $serializer
     */
    public function __construct(ReferralService $referralService, SerializerInterface $serializer)
    {
        $this->referralService = $referralService;
        $this->serializer = $serializer;
    }

    /**
     * @param ResolveDispute $command
     */
    public function __invoke(ResolveDispute $command)
    {
        $referral = $this->referralService->findByGuid(
            $this->serializer->denormalize(array(
                'value' => $command->getDisputeResolution()->getReferralGuid()
            ), Guid::class, 'array')
        );

        // Convert form into referral
        $resolveDispute = $this->serializer->denormalize(array(
            'score' => $referral->getRating()->getScore(),
            'ratingNote' => $referral->getRating()->getRatingNote(),
            'resolution' => $command->getDisputeResolution()->getResolution(),
            'reviewerNote' => $command->getDisputeResolution()->getReviewerNote()
        ), ReferralRating::class, 'array');

        $resolveDispute->setReviewerGuid($command->getReviewerGuid());

        $referral->setRating($resolveDispute);

        // Resolve dispute
        $this->referralService->resolveDispute($referral);

        return;
    }
}