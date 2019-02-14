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
 * Class ExpirationMonthConstraint
 *
 * @Annotation
 * @package PapaLocal\Entity\Validation
 */
class ExpirationMonthConstraint extends Constraint
{
    /**
     * @var string the default message displayed when the constraint is violated
     */
    public $message = 'Expiration month must be greater or equal to current month.';
}
