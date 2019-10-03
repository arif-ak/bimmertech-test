<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Product;

class OrderItemUnitsNormalizer
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var OrderItemUnit $orderItemUnit */
        $orderItemUnit = $object;
        /** @var OrderItem $item */
        $item = $orderItemUnit->getOrderItem();

        return [
            'id' => $orderItemUnit->getId(),
            'product_name' => $item->getProductName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItemUnit;
    }
}
