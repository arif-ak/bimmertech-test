<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Taxon;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CategoryNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class CategoryNormalizer implements NormalizerInterface
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
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'shortDescription' => $object->getShortDescription(),
            'description' => $object->getDescription(),
            'isContainer' => $object->isContainer(),
            'price' => $object->getPrice(),
            'bestseller' => [
                'name' => $object->getBestseller() ? $object->getBestseller()->getName() : null,
                'color' => $object->getBestseller() ? $object->getBestseller()->getColor() : null,
            ],
            'images' => array_map(function ($image) {
                return '/media/image/' . $image->getPath();
            }, $object->getImages()->toArray())
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Taxon;
    }
}