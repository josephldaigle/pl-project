<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 4/29/18
 * Time: 8:11 AM
 */

namespace PapaLocal\Entity\Validation;

use PapaLocal\Entity\Billing\CreditCard;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExpirationMonthConstraintValidator
 *
 * @package PapaLocal\Entity\Validation
 */
class ExpirationMonthConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $expirationMonth = $this->context->getRoot()->getExpirationMonth();
        $expirationYear = $this->context->getRoot()->getExpirationYear();

        if (($expirationYear != null) && (date('y') == $expirationYear) && (date('m') > $expirationMonth)) {

            $this->context->buildViolation($constraint->message)
                 ->setParameter('{{ string }}', $value)
                 ->addViolation();

        }
    }
}
