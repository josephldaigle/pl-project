<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/11/18
 * Time: 7:59 PM
 */

namespace PapaLocal\Core\Validation;


use Symfony\Component\Validator\Constraint;


/**
 * Class BeforeNow
 *
 * @Annotation
 *
 * @package PapaLocal\Core\Validation
 */
class BeforeNow extends Constraint
{
    const AFTER_NOW_ERROR = '92ea021a-79fd-45ef-8428-17cfbab4f368';

    protected static $errorNames = array(
        self::AFTER_NOW_ERROR => 'AFTER_NOW_ERROR',
    );

    public $message = 'The date {{ value }} must be before the current time.';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}