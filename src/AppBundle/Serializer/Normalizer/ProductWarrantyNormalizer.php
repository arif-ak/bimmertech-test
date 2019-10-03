<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductWarrantyNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ProductWarrantyNormalizer implements NormalizerInterface
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
                return (new ProductNormalizer())->normalize($product, 'json', $context);
            }, $products);
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
