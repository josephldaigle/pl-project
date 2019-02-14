<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 11:24 AM
 */

namespace PapaLocal\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * StorageNormalizer.
 *
 * Provides normalization for object going into storage.
 * Overrides the PropertyNormalizer::normalize() function to filter
 * out null properties. This ensures update statements don't accidentally
 * overwrite non-present values with null.
 */
class StorageNormalizer extends PropertyNormalizer
{
    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $format === 'array';
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = $this->callParentNormalizer($object, $format, $context);

        if (isset($context['attributes'])) {
            foreach (array_keys($data) as $field) {
                // client specified which fields to use, remove all others
                if (! in_array($field, $context['attributes'])) {
                    unset($data[$field]);
                }
            }

            return $this->filterNormalizedData($data);
        }

        // no context provided
        return $this->filterNormalizedData($data);

    }

    private function callParentNormalizer($object, $format = null, array $context = array())
    {
        return parent::normalize($object, $format, $context);
    }

    /**
     * Default data filtering (removes empty or null values).
     *
     * @param array $data
     *
     * @return array
     */
    private function filterNormalizedData(array $data)
    {
        return array_filter($data, function($value) {
            return null !== $value &&  !empty($value);
        });
    }
}