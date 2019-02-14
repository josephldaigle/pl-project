<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 4/29/18
 * Time: 8:11 AM
 */

namespace PapaLocal\Entity\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExpirationYearConstraintValidator
 *
 * @package PapaLocal\Entity\Validation
 */
class ExpirationYearConstraintValidator extends ConstraintValidator
{
    public function validate($expirationYear, Constraint $constraint)
    {
        if ($expirationYear != null && date('y') > $expirationYear) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $expirationYear)
                ->addViolation();
        }
    }
}

