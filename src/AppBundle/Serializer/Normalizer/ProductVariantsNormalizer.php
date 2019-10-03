<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\ProductVariant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductVariantsNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ProductVariantsNormalizer implements NormalizerInterface
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
            return array_map(function ($variant) use ($context) {
                /** @var ProductVariant $variant */
                return [
                    'id' => $variant->getId(),
                    'name' => $variant->getName(),
                    'price' => $variant->getChannelPricingForChannel($context['channel'])->getPrice(),
                    'originalPrice' => $variant->getChannelPricingForChannel($context['channel'])->getOriginalPrice(),
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
        return $data instanceof ProductVariant;
    }
}