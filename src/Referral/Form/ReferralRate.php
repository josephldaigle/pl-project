<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 9/14/18
 * Time: 12:25 PM
 */

namespace PapaLocal\Referral\Form;


use PapaLocal\Core\Validation\CannotDispute;
use PapaLocal\Referral\Validation as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ReferralRate
 * @package PapaLocal\Referral\Form
 *
 * @AppAssert\ScoreConstraint(
 *     message="This referral cannot be rated lower than three (3) stars."
 * )
 *
 * @CannotDispute()
 */
class ReferralRate
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
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "The rating cannot be blank."
     * )
     *
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "The rate must be at least {{ limit }} stars.",
     *      maxMessage = "The rate must be at most {{ limit }} stars."
     * )
     */
    private $referralRate;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The feedback cannot be blank."
     * )
     */
    private $referralFeedback;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * ReferralRate constructor.
     * @param string $referralGuid
     * @param int|null $referralRate
     * @param string $referralFeedback
     * @param string $timeCreated
     */
    public function __construct(string $referralGuid = '', int $referralRate = null, string $referralFeedback = '', string $timeCreated = '')
    {
        $this->referralGuid = $referralGuid;
        $this->referralRate = $referralRate;
        $this->referralFeedback = $referralFeedback;
        $this->timeCreated = $timeCreated;
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
    public function getReferralRate()
    {
        return $this->referralRate;
    }

    /**
     * @return mixed
     */
    public function getReferralFeedback()
    {
        return $this->referralFeedback;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }
}