<?php

/**
 * Created by PhpStorm.
 * Date: 10/12/18
 * Time: 7:52 AM
 */

namespace PapaLocal\Referral\Message\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Form\DisputeResolution;


/**
 * Class ResolveDispute
 * @package PapaLocal\Referral\Message\Command
 */
class ResolveDispute
{
    /**
     * @var DisputeResolution
     */
    private $disputeResolution;

    /**
     * @var Guid
     */
    private $reviewerGuid;

    /**
     * ResolveDispute constructor.
     * @param DisputeResolution $disputeResolution
     * @param Guid $reviewerGuid
     */
    public function __construct(DisputeResolution $disputeResolution, Guid $reviewerGuid)
    {
        $this->disputeResolution = $disputeResolution;
        $this->reviewerGuid = $reviewerGuid;
    }

    /**
     * @return DisputeResolution
     */
    public function getDisputeResolution(): DisputeResolution
    {
        return $this->disputeResolution;
    }

    /**
     * @return Guid
     */
    public function getReviewerGuid(): Guid
    {
        return $this->reviewerGuid;
    }

}