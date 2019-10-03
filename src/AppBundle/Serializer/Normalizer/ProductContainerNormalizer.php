<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class ProductContainerNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'compatibility' => $object->getCompatibility(),
            'slug' => $object->getSlug(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}