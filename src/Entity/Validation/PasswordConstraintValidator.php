<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/5/17
 * Time: 8:44 AM
 */

namespace PapaLocal\Entity\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PasswordConstraintValidator.
 *
 * @package PapaLocal\Entity\Validation
 */
class PasswordConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (! preg_match('/[A-Z]/', $value, $matches)){
            $this->context->buildViolation('The password field must contain at least one uppercase letter.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (! preg_match('/[a-z]/', $value, $matches)){
            $this->context->buildViolation('The password field must contain at least one lowercase letter.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (! preg_match('/\d/', $value, $matches)){
            $this->context->buildViolation('The password field must contain at least one number.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (! preg_match('/[~!@#$%^&*()]/', $value, $matches)) {
            $this->context->buildViolation('The password field must contain at least one special character.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (! preg_match('/^.{8,128}$/', $value, $matches)) {
            $this->context->buildViolation('The password field must contain at least 8 and at most 128 characters.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

        if (preg_match('/\s/', $value, $matches)) {
            $this->context->buildViolation('The password field must not contain spaces.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}