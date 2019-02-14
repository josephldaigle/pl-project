<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/5/17
 * Time: 8:25 AM
 */

namespace PapaLocal\Referral\Validation;


use Symfony\Component\Validator\Constraint;


/**
 * Class ScoreConstraint
 * @package PapaLocal\Referral\Validation
 * @Annotation
 */
class ScoreConstraint extends Constraint
{
    const IS_INVALID_SCORE = 'e0313871-457d-4618-8e4e-784e8c8445d0';

    protected static $errorNames = array(
        self::IS_INVALID_SCORE => 'IS_INVALID_SCORE',
    );

    /**
     * @var string
     *
     * the default message displayed when the constraint is violated
     */
    public $message = 'This referral cannot be rated lower than three (3) stars.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}