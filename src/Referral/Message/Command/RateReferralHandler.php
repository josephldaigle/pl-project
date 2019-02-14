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
 * Class RateReferralHandler
 * @package PapaLocal\Referral\Message\Command
 */
class RateReferralHandler
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
     * RateReferralHandler constructor.
     * @param ReferralService $referralService
     * @param SerializerInterface $serializer
     */
    public function __construct(ReferralService $referralService, SerializerInterface $serializer)
    {
        $this->referralService = $referralService;
        $this->serializer = $serializer;
    }

    /**
     * @param RateReferral $command
     */
    public function __invoke(RateReferral $command)
    {
        $referral = $this->referralService->findByGuid(
            $this->serializer->denormalize(array(
                'value' => $command->getReferralRate()->getReferralGuid()
            ), Guid::class, 'array')
        );

        // Convert form into referral
        $referralRate = $this->serializer->denormalize(array(
            'score' => $command->getReferralRate()->getReferralRate(),
            'ratingNote' => $command->getReferralRate()->getReferralFeedback()
        ), ReferralRating::class, 'array');

        $referral->setRating($referralRate);

        // Rate referral
        $this->referralService->rateReferral($referral);

        return;
    }
}