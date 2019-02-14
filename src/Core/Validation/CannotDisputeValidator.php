<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/12/18
 * Time: 11:36 AM
 */

namespace PapaLocal\Core\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class CannotDisputeValidator
 * @package PapaLocal\Core\Validation
 */
class CannotDisputeValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $today = new \DateTime(date('Y-m-d H:i:s', time()));

        $expirationDate = new \DateTime($value->getTimeCreated());
        $interval = new \DateInterval('PT72H');
        $expirationDate->add($interval);


        if ($value->getReferralRate() < 3 && ($today > $expirationDate)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(CannotDispute::CANNOT_DISPUTE_ERROR)
                ->addViolation();
        }

        return;
    }
}