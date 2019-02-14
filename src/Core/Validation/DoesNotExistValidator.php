<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 11:32 PM
 */


namespace PapaLocal\Core\Validation;


use PapaLocal\Core\Service\MessageFactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


/**
 * Class DoesNotExistValidator
 *
 * @package PapaLocal\Core\Validation
 */
class DoesNotExistValidator extends ConstraintValidator
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var MessageFactoryInterface
     */
    private $messageFactory;

    /**
     * DoesNotExistValidator constructor.
     *
     * @param MessageBusInterface $messageBus
     * @param                     $messageFactory
     */
    public function __construct(MessageBusInterface $messageBus,
                                MessageFactoryInterface $messageFactory)
    {
        $this->messageBus     = $messageBus;
        $this->messageFactory = $messageFactory;
    }


    /**
     * @param mixed      $value
     * @param Constraint $constraint
     *
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DoesNotExist) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\DoesNotExist');
        }

        try {
            $query = $this->messageFactory->newFindByGuid($value);
            $qryResult = $this->messageBus->dispatch($query);   // throws exception when not found

            // entity found, therefore constraint fails (guid does exist)
            $this->context->buildViolation($constraint->message)
                  ->setParameter('{{ value }}', $this->formatValue($value->value()))
                  ->setParameter('{{ type }}', $this->formatValue(
                      isset($constraint->payload['type']) ? $constraint->payload['type'] : '[unknown]'
                  ))
                  ->setCode(DoesNotExist::EXISTS_ERROR)
                  ->addViolation();

        } catch (\Exception $exception) {
            // guid is unique, no validation rule was broken
            return;
        }
    }
}