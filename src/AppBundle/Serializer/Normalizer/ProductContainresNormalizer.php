<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Taxon;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductContainresNormalizer
 * @package AppBundle\Serializer\Normalizer
 *
 */
class ProductContainresNormalizer implements NormalizerInterface
{

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $imageSize = isset($context['image_size']) ? $context['image_size'] : null;

        $imageObject = $object->getImages()->first();
        if ($imageObject && $imageSize == "product_list") {
            $image = '/media/cache/resolve/sylius_shop_product_thumbnail/' . $imageObject->getPath();
        } else {
            $image = '/images/no-image.png';
        }

        $rating = isset($context['rating']) ? $context['rating'] : null;

        if ($object) {
            /** @var Taxon $object */
            $taxon = [
                'id' => $object->getId(),
                'name' => $object->getName(),
                'description' => $object->getDescription(),
                'slug' => $object->getSlug(),
                'taxons' => true,
                'price' => $object->getPrice(),
                'bestseller' => [
                    'name' => $object->getBestseller() ? $object->getBestseller()->getName() : null,
                    'color' => $object->getBestseller() ? $object->getBestseller()->getColor() : null,
                ],
                'compatibility' => $object->getCompatibility(),
                'images' => $imageSize == "product_list" ? $image :
                    array_map(function ($image) {
                        return '/media/image/' . $image->getPath();
                    }, $object->getImages()->toArray())
            ];

            $taxon['ratings'] = [
                count($rating) > 0 ? (int)$rating[0]['rating_5'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_4'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_3'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_2'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_1'] : 0,
            ];
            $taxon['averageRating'] = count($rating) > 0 ? (int)$rating[0]['avg_rating'] : 0;
            $taxon['reviewCount'] = count($rating) > 0 ? (int)$rating[0]['reviewCount'] : 0;

            return $taxon;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Taxon;
    }
}