<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 7:31 AM
 */

namespace PapaLocal\Serializer\NameConverter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * DormantConverter.
 *
 * This class overrides the default behavior of Syfmony's serializer, which
 * is to convert object property names to snake_case during normalization.
 *
 * The
 */
class DormantConverter implements NameConverterInterface
{
    private $attributes;

    /**
     * @param array|null $attributes    The list of attributes to rename or null for all attributes
     */
    public function __construct(array $attributes = null)
    {
        $this->attributes = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function normalize($propertyName)
    {
        return $propertyName;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($propertyName)
    {
        return $propertyName;
    }

}