<?php

namespace AppBundle\Serializer\Normalizer;

use Sylius\Component\Product\Model\ProductAttribute;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductAttributesNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class ProductAttributesNormalizer implements NormalizerInterface
{

    /**
     * {@inheritdoc}
     *
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {

        if ($object) {
            return array_map(function ($attribute) {
                /** @var ProductAttribute $attribute */
                return [
                    'id' => $attribute->getId(),
                    'name' => $attribute->getName(),
                    'value' => $attribute->getValue()
                ];
            }, $object->toArray());
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductAttribute;
    }
}