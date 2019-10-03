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
class InterestedInNormalizer implements NormalizerInterface
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
        $price = null;
        if (isset($context['channel'])) {
            $channelPricing = $variant->getChannelPricingForChannel($context['channel']);
            $price = $channelPricing ? $channelPricing->getPrice() : null;
        }

        $image = '/images/no-image.png';
        if ($object->getImages()->count()) {
            $image = '/media/cache/resolve/product_570_367/' . $object->getImages()->first()->getPath();
        }

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'price' => $price,
            'image' => $image,
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
