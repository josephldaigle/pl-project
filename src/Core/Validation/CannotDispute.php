<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/12/18
 * Time: 11:36 AM
 */

namespace PapaLocal\Core\Validation;


use Symfony\Component\Validator\Constraint;


/**
 * Class CannotDispute
 * @package PapaLocal\Core\Validation
 * @Annotation
 */
class CannotDispute extends Constraint
{
    const CANNOT_DISPUTE_ERROR = 'c3b5f008-8de3-48e4-bccf-5e097617ffe3';

    protected static $errorNames = array(
        self::CANNOT_DISPUTE_ERROR => 'CANNOT_DISPUTE_ERROR',
    );

    /**
     * @var string
     *
     * the default message displayed when the constraint is violated
     */
    public $message = 'The referral cannot be dispute after 72 hours.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}