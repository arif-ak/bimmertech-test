<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\ProductProperty;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductPropertiesNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ProductPropertiesNormalizer implements NormalizerInterface
{

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if ($object) {
            return array_map(function ($property) {
                /** @var ProductProperty $property */
                return [
                    'id' => $property->getId(),
                    'name' => $property->getName(),
                    'description' => $property->getDescription(),
                    'images' => $property->getImages()->count() > 0 ?
                        $property->getImages()->first()->getPath() : null,
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
        return $data instanceof ProductProperty;
    }

}