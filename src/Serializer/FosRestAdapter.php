<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/9/18
 * Time: 8:58 PM
 */

namespace PapaLocal\Serializer;


use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Serializer\Serializer as FosRestSerializer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class FosRestAdapter
 *
 * @package PapaLocal\Serializer
 */
class FosRestAdapter implements FosRestSerializer
{
	private $serializer;

	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize($data, $format, Context $context)
	{
		$newContext = $this->convertContext($context);
		$newContext['serializeNull'] = $context->getSerializeNull();

		return $this->serializer->serialize($data, $format, $newContext);
	}

	/**
	 * {@inheritdoc}
	 */
	public function deserialize($data, $type, $format, Context $context)
	{
		$newContext = $this->convertContext($context);

		return $this->serializer->deserialize($data, $type, $format, $newContext);
	}

	/**
	 * @inheritdoc
	 * @throws \BadFunctionCallException
	 */
	public function denormalize($data, $class, $format = null, array $context = array())
	{
		if (! $this->serializer instanceof DenormalizerInterface) {
			throw new \BadFunctionCallException(sprintf('%s cannot be called because the serializer being used is not an instance of %s.', __METHOD__, DenormalizerInterface::class));
		}
		return $this->serializer->denormalize($data, $class, $format, $context);
	}

	/**
	 * @inheritdoc
	 */
	public function supportsDenormalization($data, $type, $format = null)
	{
		if (! $this->serializer instanceof DenormalizerInterface) {
			return false;
		}
		return $this->serializer->supportsDenormalization($data, $type, $format);
	}

	/**
	 * @param Context $context
	 */
	private function convertContext(Context $context)
	{
		$newContext = array();
		foreach ($context->getAttributes() as $key => $value) {
			$newContext[$key] = $value;
		}

		if (null !== $context->getGroups()) {
			$newContext['groups'] = $context->getGroups();
		}
		$newContext['version'] = $context->getVersion();
		$newContext['maxDepth'] = $context->getMaxDepth(false);
		$newContext['enable_max_depth'] = $context->isMaxDepthEnabled();

		return $newContext;
	}

}