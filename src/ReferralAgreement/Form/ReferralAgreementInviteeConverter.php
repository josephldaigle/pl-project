<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/20/18
 */


namespace PapaLocal\ReferralAgreement\Form;


use PapaLocal\Core\ValueObject\EmailAddressType;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Request\RequestBodyParamConverter;
use FOS\RestBundle\Serializer\Serializer;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\Exception\Exception as JMSSerializerException;
use JMS\Serializer\Exception\UnsupportedFormatException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;


/**
 * Class ReferralAgreementInviteeConverter.
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class ReferralAgreementInviteeConverter extends RequestBodyParamConverter
{
    private $serializer;
    private $context = [];
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

            // convert the base object
            $form = $this->serializer->denormalize(array(
                'agreementId' => array('value' => $request->request->get('agreementId', '')),
                'firstName' => $request->request->get('firstName', ''),
                'lastName' => $request->request->get('lastName', ''),
                'message' => $request->request->get('message', ''),
                'emailAddress' => array(
                    'emailAddress' => $request->request->get('emailAddress', ''),
                    'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
                ),
                'phoneNumber' => array(
                    'phoneNumber' => $request->request->get('phoneNumber', ''),
                    'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
                )
            ), ReferralAgreementInviteeForm::class, 'array');


        } catch (UnsupportedFormatException $e) {
            return $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
        } catch (JMSSerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        } catch (SymfonySerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $request->attributes->set($configuration->getName(), $form);

        if (null !== $this->validator && (!isset($options['validate']) || $options['validate'])) {
            $validatorOptions = $this->getValidatorOptions($options);

            $errors = $this->validator->validate($form, null, array('Default'));

            if (null !== $form->getPhoneNumber()) {
                $phoneErrors = $this->validator->validate($form, null, array('include_phone'));

                $errors->addAll($phoneErrors);
            }

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
        return null !== $configuration->getClass() && 'PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeConverter' === $configuration->getConverter();
    }

    /**
     * @param Context $context
     * @param array   $options
     */
    protected function configureContext(Context $context, array $options)
    {
        foreach ($options as $key => $value) {
            if ('groups' === $key) {
                $context->addGroups($options['groups']);
            } elseif ('version' === $key) {
                $context->setVersion($options['version']);
            } elseif ('maxDepth' === $key) {
                @trigger_error('Context attribute "maxDepth" is deprecated since version 2.1 and will be removed in 3.0. Use "enable_max_depth" instead.', E_USER_DEPRECATED);
                $context->setMaxDepth($options['maxDepth']);
            } elseif ('enableMaxDepth' === $key) {
                $context->enableMaxDepth($options['enableMaxDepth']);
            } elseif ('serializeNull' === $key) {
                $context->setSerializeNull($options['serializeNull']);
            } else {
                $context->setAttribute($key, $value);
            }
        }
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