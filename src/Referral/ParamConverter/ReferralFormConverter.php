<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/1/18
 * Time: 9:21 PM
 */

namespace PapaLocal\Referral\ParamConverter;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Address;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Request\RequestBodyParamConverter;
use FOS\RestBundle\Serializer\Serializer;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
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
 * Class ReferralFormConverter
 *
 * Param converter for ReferralForm.
 *
 * @package PapaLocal\Referral\ParamConverter
 */
class ReferralFormConverter extends RequestBodyParamConverter
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
            // set recipient on form
			if ($request->request->get('agreementId')) {
                // fetch the recipient from the request
                $recipient = $this->serializer->denormalize(
                array(
                    'guid' => array('value' => $request->request->get('agreementId', '')),
                ),
                AgreementRecipient::class, 'array');
            } else {
                $recipient = $this->serializer->denormalize(
                array(
                    'firstName' => $request->request->get('recipientFirstName', ''),
                    'lastName' => $request->request->get('recipientLastName', ''),
                    'phoneNumber' => array(
                        'phoneNumber' => $request->request->get('recipientPhoneNumber', ''),
                        'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
                    ),
                    'emailAddress' => array(
                        'emailAddress' => $request->request->get('recipientEmailAddress', ''),
                        'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
                    ),
                ),
                ContactRecipient::class, 'array');
			}

            // convert the base object (ReferralForm)
            $object = $this->serializer->denormalize(
                array(
                    'firstName' => $request->request->get('firstName', ''),
                    'lastName' => $request->request->get('lastName', ''),
                    'phoneNumber' => array(
                        'phoneNumber' => $request->request->get('phoneNumber', ''),
                        'type' => array('value' => PhoneNumberType::PERSONAL()->getValue())
                    ),
                    'emailAddress' => array(
                        'emailAddress' => $request->request->get('emailAddress', ''),
                        'type' => array('value' => EmailAddressType::PERSONAL()->getValue())
                    ),
                    'address' => $request->request->get('address', array()) ,
                    'about' => $request->request->get('about', ''),
                    'note' => $request->request->get('note', ''),
                ), $configuration->getClass(), 'array', $this->context);
            $object->setRecipient($recipient);

		} catch (UnsupportedFormatException $e) {
			return $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
		} catch (JMSSerializerException $e) {
			return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
		} catch (SymfonySerializerException $e) {
			return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
		}

		$request->attributes->set($configuration->getName(), $object);

		// TODO: Rewrite to
        // validate based on user-selection (agreement or contact recipient)
		if (null !== $this->validator && (!isset($options['validate']) || $options['validate'])) {
//			$validatorOptions = $this->getValidatorOptions($options);

            if (! empty($request->request->get('agreementId'))) {
                if (! empty($request->request->get('note'))) {
                    // validate for agreement recipient with note
                    $errors = $this->validator->validate($object, null, array('Default', 'agreement', 'note'));
                } else {
                    // validate for agreement recipient
                    $errors = $this->validator->validate($object, null, array('Default', 'agreement'));
                }
            } else {
                if (! empty($request->request->get('note'))) {
                    // validate for contact recipient with note
                    $errors = $this->validator->validate($object, null, array('Default', 'contact', 'note'));
                } else {
                    // validate for contact recipient
                    $errors = $this->validator->validate($object, null, array('Default', 'contact'));
                }
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
		return null !== $configuration->getClass() && 'PapaLocal\Referral\ParamConverter\ReferralFormConverter' === $configuration->getConverter();
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