<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/5/17
 * Time: 8:44 AM
 */

namespace PapaLocal\Referral\Validation;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\ReferralService;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class ScoreConstraintValidator
 * @package PapaLocal\Referral\Validation
 */
class ScoreConstraintValidator extends ConstraintValidator
{
    /**
     * @var ReferralService
     */
    private $referralService;

    /**
     * ScoreConstraintValidator constructor.
     * @param ReferralService $referralService
     */
    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function validate($value, Constraint $constraint)
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if ($value->getReferralGuid() != null) {
            $referral = $this->referralService->findByGuid(new Guid($value->getReferralGuid()));

            if ($referral->getRecipient() instanceof ContactRecipient) {

                if ($value->getReferralRate() < 3) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(ScoreConstraint::IS_INVALID_SCORE)
                        ->addViolation();
                }
            }
        }
        return;
    }
}