<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/11/18
 * Time: 8:06 PM
 */

namespace PapaLocal\Core\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class BeforeNowValidator
 *
 * @package PapaLocal\Core\Validation
 */
class BeforeNowValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BeforeNow) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\BeforeNow');
        }

        if ($value > date('Y-m-d H:i:s', time())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(BeforeNow::AFTER_NOW_ERROR)
                ->addViolation();
        }

        return;
    }

}