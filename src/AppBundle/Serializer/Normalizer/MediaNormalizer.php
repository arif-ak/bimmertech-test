<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductVariantsNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class MediaNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Collection $images */
        if ($images = $object->getImages()) {
            return array_map(function ($image)  {
                /** @var Image $image */
                return [
                    'id' => $image->getId(),
                    'name' =>'/media/image/'. $image->getPath(),
                ];
            }, $images->toArray());
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}