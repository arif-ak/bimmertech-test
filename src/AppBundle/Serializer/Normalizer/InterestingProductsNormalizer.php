<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use Sylius\Component\Core\Model\ProductImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class InterestingProductsNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class InterestingProductsNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {

        if (!$interestingProducts = $object->getInterestingProducts()->toArray()) {
            return $interestingProducts;
        }

        /** @var Product $object */
        /** @var ProductVariant $variant */
        $variant = $object->getVariants()->first();
        $channelPricing = $variant->getChannelPricingForChannel($context['channel']);

        return array_map(function ($product) use ($channelPricing) {
            /** @var Product $product */
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getVariants()->count() > 1 ? null : $channelPricing->getPrice(),
                'images' => array_map(function ($image) {

                    /** @var ProductImage $image */
                    return '/media/image/' . $image->getPath();
                }, $product->getImages()->toArray())
            ];
        }, $interestingProducts);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}