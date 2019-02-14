<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 11:22 PM
 */


namespace PapaLocal\ReferralAgreement\Form;


use FOS\RestBundle\Request\RequestBodyParamConverter;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Serializer\Serializer;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\LocationType;
use PapaLocal\ReferralAgreement\ValueObject\Service;
use PapaLocal\ReferralAgreement\ValueObject\ServiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\Exception\Exception as JMSSerializerException;
use JMS\Serializer\Exception\UnsupportedFormatException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;


/**
 * Class CreateAgreementConverter
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class CreateAgreementConverter extends RequestBodyParamConverter
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

            // convert the base object (ReferralForm)
            // get locations
            $includedLocations = $this->convertIncludedLocations($request);
            $excludedLocations = $this->convertExcludedLocations($request);

            // get services
            $includedServices = $this->convertIncludedServices($request);
            $excludedServices = $this->convertExcludedServices($request);

            $form = $this->serializer->denormalize(
                array(
                    'name' => $request->request->get('name', ''),
                    'description' => $request->request->get('description', ''),
                    'quantity' => $request->request->get('quantity', 0),
                    'strategy' => $request->request->get('strategy', ''),
                    'bid' => $request->request->get('bid', 0.00),
                    'includedLocations' => $includedLocations,
                    'includedServices' => $includedServices,
                    'excludedLocations' => $excludedLocations,
                    'excludedServices' => $excludedServices
                ),
                CreateAgreementForm::class,
                'array');

        } catch (UnsupportedFormatException $e) {
            return $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
        } catch (JMSSerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        } catch (SymfonySerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $request->attributes->set($configuration->getName(), $form);

        if (false == $request->request->has('discardContinue')) {
            // do not validate when user requests not to save invitee.
            if (null !== $this->validator && (!isset($options['validate']) || $options['validate'])) {
                $validatorOptions = $this->getValidatorOptions($options);

                // todo validation
                $errors = $this->validator->validate($form, null, $validatorOptions['groups']);

                $request->attributes->set(
                    $this->validationErrorsArgument,
                    $errors
                );
            }
        } else {
            $request->attributes->set(
                $this->validationErrorsArgument,
                new ConstraintViolationList()
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return null !== $configuration->getClass() && 'PapaLocal\ReferralAgreement\Form\CreateAgreementConverter' === $configuration->getConverter() && $configuration->getName() === 'form';
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

    /**
     * Converts contents of includedLocations array into Location instances.
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function convertIncludedLocations(Request $request)
    {
        // embed included locations
        $locations = $request->request->get('includedLocations', []);

        for($i = 0; $i < count($locations); $i++) {
            $location = $this->serializer->denormalize(array(
                'location' => $locations[$i],
                'type' => array('value' => LocationType::INCLUDE()->getValue()),
                ), Location::class, 'array');
            $locations[$i] = $location;
        }

        return $locations;
    }

    /**
     * Converts contents of excludedLocations array into Location instances.
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function convertExcludedLocations(Request $request)
    {
        // embed excluded locations
        $locations = $request->request->get('excludedLocations', []);
        for($i = 0; $i < count($locations); $i++) {
            $location = $this->serializer->denormalize(array(
                'location' => $locations[$i],
                'type' => array('value' => LocationType::EXCLUDE()->getValue())
            ), Location::class, 'array');
            $locations[$i] = $location;
        }

        return $locations;
    }

    /**
     * Converts contents of includedServices array into Services instances.
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function convertIncludedServices(Request $request)
    {
        $services = $request->request->get('includedServices', []);
        for($i = 0; $i < count($services); $i++) {
            $service = $this->serializer->denormalize(array(
                'service' => $services[$i],
                'type' => array('value' => ServiceType::INCLUDE()->getValue())
            ), Service::class, 'array');
            $services[$i] = $service;
        }

        return $services;
    }

    /**
     * Converts contents of includedServices array into Services instances.
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function convertExcludedServices(Request $request)
    {
        $services = $request->request->get('excludedServices', []);
        for($i = 0; $i < count($services); $i++) {
            $service = $this->serializer->denormalize(array(
                'service' => $services[$i],
                'type' => array('value' => ServiceType::EXCLUDE()->getValue())
            ), Service::class, 'array');
            $services[$i] = $service;
        }

        return $services;
    }
}