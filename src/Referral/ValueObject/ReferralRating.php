<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/23/17
 * Time: 10:41 AM
 */

namespace PapaLocal\Referral\ValueObject;


use PapaLocal\Core\ValueObject\Guid;


/**
 * ReferralRating.
 *
 * @package PapaLocal\Entity
 */
class ReferralRating
{
    /**
     * @var int
     */
    private $score;

    /**
     * @var string
     */
    private $ratingNote;

    /**
     * @var string
     */
    private $resolution;

    /**
     * @var Guid
     */
    private $reviewerGuid;

    /**
     * @var string
     */
    private $reviewerNote;

    /**
     * ReferralRating constructor.
     * @param int $score
     * @param string $ratingNote
     * @param string $resolution
     * @param Guid|null $reviewerGuid
     * @param string $reviewerNote
     */
    public function __construct(int $score,
                                string $ratingNote,
                                string $resolution = '',
                                Guid $reviewerGuid = null,
                                string $reviewerNote = '')
    {
        $this->score = $score;
        $this->ratingNote = $ratingNote;
        $this->resolution = $resolution;
        $this->reviewerGuid = $reviewerGuid;
        $this->reviewerNote = $reviewerNote;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getRatingNote(): string
    {
        return $this->ratingNote;
    }

    /**
     * @return string
     */
    public function getResolution(): string
    {
        return $this->resolution;
    }

    /**
     * @param string $resolution
     * @return ReferralRating
     */
    public function setResolution(string $resolution): ReferralRating
    {
        $this->resolution = $resolution;
        return $this;
    }

    /**
     * @return Guid
     */
    public function getReviewerGuid(): Guid
    {
        return $this->reviewerGuid;
    }

    /**
     * @param Guid $reviewerGuid
     * @return ReferralRating
     */
    public function setReviewerGuid(Guid $reviewerGuid): ReferralRating
    {
        $this->reviewerGuid = $reviewerGuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getReviewerNote(): string
    {
        return $this->reviewerNote;
    }

    /**
     * @param string $reviewerNote
     * @return ReferralRating
     */
    public function setReviewerNote(string $reviewerNote): ReferralRating
    {
        $this->reviewerNote = $reviewerNote;
        return $this;
    }
}
