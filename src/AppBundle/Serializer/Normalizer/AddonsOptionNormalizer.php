<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use Sylius\Component\Product\Model\ProductAssociation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PopupOptionNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class AddonsOptionNormalizer implements NormalizerInterface
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
        return $this->getAddons($object->getAssociations(), $context['type']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }

    /**
     * @param $type
     * @return mixed
     */
    private function getAddons($associations, $type)
    {
        /** @var ProductAssociation $association */
        $association = $associations
            ->filter(function ($association) use ($type) {
                if ($association->getType()->getCode() == $type) {
                    return $association;
                }
            })
            ->first();
        if ($association) {
            $products = $association->getAssociatedProducts()
                ->toArray();
            return array_map(function ($product) {

                /** @var Product $product */
                return [
                    'id' => $product->getId(),
                    'code' => $product->getCode(),
                    'compatibility' => $product->getCompatibility()
                ];
            }, $products);
        }
        return [];
    }
}
