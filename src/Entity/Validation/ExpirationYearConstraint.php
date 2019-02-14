<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 4/29/18
 * Time: 8:04 AM
 */

namespace PapaLocal\Entity\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Class ExpirationYearConstraint
 *
 * @Annotation
 * @package PapaLocal\Entity\Validation
 */
class ExpirationYearConstraint extends Constraint
{
    /**
     * @var string the default message displayed when the constraint is violated
     */
    public $message = 'Expiration year must be greater or equal to current year.';
}
