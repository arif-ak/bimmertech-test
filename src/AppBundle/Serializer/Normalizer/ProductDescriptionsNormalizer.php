<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\ProductDescription;
use AppBundle\Entity\ProductProperty;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductDescriptionsNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ProductDescriptionsNormalizer implements NormalizerInterface
{

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Collection $descriptions */

        if ($descriptions = $object->getProductDescriptions()) {
            return array_map(function ($description) {
                /** @var ProductDescription $description */
                return [
                    'id' => $description->getId(),
                    'title' => $description->getHeader(),
                    'subHeader' => $description->getSubHeader(),
                    'description' => $description->getDescription(),
                    'type' => $description->getType(),
                    'images' => $description->getImages()->count() > 0 ?
                        $description->getImages()->first()->getPath() : null,
                ];
            }, $descriptions->toArray());
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
