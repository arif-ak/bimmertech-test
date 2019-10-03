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
class ProductNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Product $object */
        /** @var ProductVariant $variant */
        $variant = $object->getVariants()->first();

        if (isset($context['channel'])) {
            $channelPricing = $variant->getChannelPricingForChannel($context['channel']);
        }

        $imageSize = isset($context['image_size']) ? $context['image_size'] : null;

        $imageObject = $object->getImages()->first();
        if ($imageObject && $imageSize == "product_list") {
            $image = '/media/cache/sylius_shop_product_thumbnail/' . $imageObject->getPath();
        } else {
            $image = null;
        }

        return [
            'id' => $object->getId(),
            'compatibility' => $object->getCompatibility(),
            'variant_id' => $variant->getId(),
            'type' => $object->getVariants()->count() > 1 ? 'configurable' : 'simple',
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'addonDescription'=> $object->getAddonInformation(),
            'is_recommended' => $object->getRecomended(),
            'shortDescription' => $object->getShortDescription(),
            'description' => $object->getDescription(),
            'installation' => $object->getInstaller(),
            'price' => $object->getVariants()->count() > 1 ? null : !$channelPricing ? null:$channelPricing->getPrice(),
            'originalPrice' => $object->getVariants()->count() > 1 ? null :!$channelPricing?null:$channelPricing->getOriginalPrice(),
            'images' => $imageSize == "product_list" ? $image :
                array_map(function ($image) {
                    return '/media/image/' . $image->getPath();
                }, $object->getImages()->toArray())
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
