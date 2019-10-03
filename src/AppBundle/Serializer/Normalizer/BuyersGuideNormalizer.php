<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\BuyersGuideProductRelated;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class BuyersGuideNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $compatibilityArray = [];

        $currentProductArray = [
            'compatibility' => $object ?
                $object->getCompatibility() : null,
        ];

        /** @var Product $object */
        $relatedProducts = $object->getBuyersRelated()->count() > 0 ? $object->getBuyersRelated() : [];
        foreach ($relatedProducts as $relatedProduct) {
            $relatedProductOptionArray = [
                'compatibility' => $relatedProduct->getRelatedProduct() ?
                    $relatedProduct->getRelatedProduct()->getCompatibility() : ProductInterface::COMPATIBILITY_NO,
            ];

            $compatibilityArray[] = $relatedProductOptionArray;
        }

        $compatibilityArray[] = $currentProductArray;

        return $compatibilityArray;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}