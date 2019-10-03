<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class ProductListNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Product $product */
        /** @var ProductVariant $variant */
        $product = $object[0];
        $variant = $product->getVariants()->first();
        $channelPricing = $variant->getChannelPricingForChannel($context['channel']);
        $imageSize = isset($context['image_size']) ? $context['image_size'] : null;

        $imageObject = $product->getImages()->first();
        if ($imageObject && $imageSize == "product_list") {
            $image = '/media/cache/resolve/sylius_shop_product_thumbnail/' . $imageObject->getPath();
        } else {
            $image = '/images/no-image.png';
        }

        return [
            'id' => $product->getId(),
            'compatibility' => $product->getCompatibility(),
            'variant_id' => $variant->getId(),
            'type' => $product->getVariants()->count() > 1 ? 'configurable' : 'simple',
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'taxons' => false,
            'is_recommended' => $product->getRecomended(),'price' => $product->getVariants()->count() > 1 ? null : $channelPricing->getPrice(),
            'originalPrice' => $product->getVariants()->count() > 1 ? null : $channelPricing->getOriginalPrice(),
            'ratings' => [
                (int)$object['rating_5'],
                (int)$object['rating_4'],
                (int)$object['rating_3'],
                (int)$object['rating_2'],
                (int)$object['rating_1'],
            ],
            'shortDescription' => $product->getShortDescription(),
            'averageRating' => $object['avg_rating'],
            'reviewCount' => $object['reviewCount'],
            'images' => $imageSize == "product_list" ? $image :
                array_map(function ($image) {
                    return '/media/image/' . $image->getPath();
                }, $product->getImages()->toArray())
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