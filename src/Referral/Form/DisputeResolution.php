<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 9/20/18
 * Time: 12:02 PM
 */

namespace PapaLocal\Referral\Form;


use PapaLocal\Core\ValueObject\Guid;
use Symfony\Component\Validator\Constraints as Assert;


class DisputeResolution
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The referral id cannot be blank."
     * )
     */
    private $referralGuid;

    /**
     * @var string
     *
     * @Assert\Choice({"approved", "denied"})
     */
    private $resolution;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The feedback cannot be blank."
     * )
     */
    private $reviewerNote;

    /**
     * DisputeResolution constructor.
     * @param string|null $referralGuid
     * @param string|null $resolution
     * @param string|null $reviewerNote
     */
    public function __construct(string $referralGuid = null,
                                string $resolution = null,
                                string $reviewerNote = null)
    {
        $this->referralGuid = $referralGuid;
        $this->resolution = $resolution;
        $this->reviewerNote = $reviewerNote;
    }

    /**
     * @return mixed
     */
    public function getReferralGuid()
    {
        return $this->referralGuid;
    }

    /**
     * @return mixed
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @return mixed
     */
    public function getReviewerNote()
    {
        return $this->reviewerNote;
    }

}