<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductWarrantyNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class OrderItemWarrantiesNormalizer implements NormalizerInterface
{
    /**
     * @param Product $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {

        $associations = $object->getAssociations();
        $products = $associations->filter(function ($association) {
            if ($association->getType()->getCode() == 'Warranty') {
                return $association;
            }
        })->first();

        if ($products) {
            $products = $products->getAssociatedProducts()
                ->toArray();

            return array_map(function ($product) use ($context) {
                return $this->prepareArray($product, $context);
            }, $products);
        }

        return [];
    }

    /**
     * @param $object
     * @param $context
     * @return array
     */
    private function prepareArray($object, $context)
    {

        /** @var Product $object */
        $variant = $object->getVariants()->first();
        $channelPricing = $variant->getChannelPricingForChannel($context['channel']);
        $price = $object->getVariants()->count() > 1 ? null : $channelPricing->getPrice() == 1 ? 0 : $channelPricing->getPrice();

        return [
            'warrantyId' => $variant->getId(),
            'name' => $object->getName(),
            'price' => $price,
            'originalPrice' => $object->getVariants()->count() > 1 ? null : $channelPricing->getOriginalPrice(),
            'compatibility' => $object->getCompatibility()
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
