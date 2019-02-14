<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 11:29 PM
 */


namespace PapaLocal\Core\Validation;


use Symfony\Component\Validator\Constraint;


/**
 * Class DoesNotExist
 *
 * @Annotation
 *
 * Ensures an Entity does not exist in the context within which it is persisted.
 *
 * @package PapaLocal\Core\Validation
 */
class DoesNotExist extends Constraint
{
    const EXISTS_ERROR = 'c1051bb4-d103-4f74-8988-acbcafc7fdc3';

    public $message = 'An {{ type }} exists with GUID {{ value }}.';

    protected static $errorNames = array(
        self::EXISTS_ERROR => 'EXISTS_ERROR',
    );

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}