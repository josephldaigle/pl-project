<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/29/18
 */


namespace PapaLocal\Billing\Form\ParamConverter;


use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Serializer\Serializer;
use FOS\RestBundle\Context\Context;
use JMS\Serializer\Exception\UnsupportedFormatException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use JMS\Serializer\Exception\Exception as JMSSerializerException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;
use FOS\RestBundle\Request\RequestBodyParamConverter;


/**
 * Class DepositFundsParamConverter.
 *
 * @package PapaLocal\Billing\Form\ParamConverter
 */
class DepositFundsParamConverter extends RequestBodyParamConverter
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * The name of the argument on which the ConstraintViolationList will be set.
     *
     * @var null|string
     */
    private $validationErrorsArgument;

    /**
     * @param Serializer         $serializer
     * @param array|null         $groups                   An array of groups to be used in the serialization context
     * @param string|null        $version                  A version string to be used in the serialization context
     * @param ValidatorInterface $validator
     * @param string|null        $validationErrorsArgument
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Serializer $serializer,
        $groups = null,
        $version = null,
        ValidatorInterface $validator = null,
        $validationErrorsArgument = null
    ) {
        parent::__construct($serializer, $groups, $version, $validator, $validationErrorsArgument);

        $this->serializer = $serializer;

        if (!empty($groups)) {
            $this->context['groups'] = (array) $groups;
        }

        if (!empty($version)) {
            $this->context['version'] = $version;
        }

        if (null !== $validator && null === $validationErrorsArgument) {
            throw new \InvalidArgumentException('"$validationErrorsArgument" cannot be null when using the validator');
        }

        $this->validator = $validator;
        $this->validationErrorsArgument = $validationErrorsArgument;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = (array) $configuration->getOptions();

        if (isset($options['deserializationContext']) && is_array($options['deserializationContext'])) {
            $arrayContext = array_merge($this->context, $options['deserializationContext']);
        } else {
            $arrayContext = $this->context;
        }
        $this->configureContext($context = new Context(), $arrayContext);

        try {

            $object = $this->serializer->deserialize(
                $request->getContent(),
                $configuration->getClass(),
                $request->getContentType(),
                $context
            );

        } catch (UnsupportedFormatException $e) {
            return $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
        } catch (JMSSerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        } catch (SymfonySerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $request->attributes->set($configuration->getName(), $object);

        if (null !== $this->validator && (!isset($options['validate']) || $options['validate'])) {
            $validatorOptions = $this->getValidatorOptions($options);

            $errors = $this->validator->validate($object, null, $validatorOptions['groups']);

            $request->attributes->set(
                $this->validationErrorsArgument,
                $errors
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return null !== $configuration->getClass() && 'PapaLocal\Billing\Form\ParamConverter\DepositFundsParamConverter' === $configuration->getConverter() && $configuration->getName() === 'depositForm';
    }

    /**
     * Throws an exception or return false if a ParamConverter is optional.
     */
    private function throwException(\Exception $exception, ParamConverter $configuration)
    {
        if ($configuration->isOptional()) {
            return false;
        }

        throw $exception;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getValidatorOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'groups' => null,
            'traverse' => false,
            'deep' => false,
        ]);

        return $resolver->resolve(isset($options['validator']) ? $options['validator'] : []);
    }
}