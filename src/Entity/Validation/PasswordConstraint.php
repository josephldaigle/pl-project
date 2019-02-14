<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/5/17
 * Time: 8:25 AM
 */

namespace PapaLocal\Entity\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * PasswordConstraint.
 *
 * @Annotation
 * @package PapaLocal\Entity\Validation
 */
class PasswordConstraint extends Constraint
{
    /**
     * @var string the default message displayed when the constraint is violated
     */
    public $message = 'The password "{{ string }}" is not a valid password.';
}