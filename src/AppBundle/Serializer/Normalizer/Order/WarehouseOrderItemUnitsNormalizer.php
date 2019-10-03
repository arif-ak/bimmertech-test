<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WarehouseOrderItemUnitsNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PayPalService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $arrayWarehouseOrderItemUnits = [];
        $warehouseOrderItemUnits = $object;

        $isNeedCheckShipment = isset($context['checkShipment']) ? $context['checkShipment'] : false;

        /** @var OrderItemUnit $item */
        if (count($warehouseOrderItemUnits) > 0) {
            foreach ($warehouseOrderItemUnits as $item) {
                $type = $item->getOrderItem()->getType();

                /** @var Product $product */
                $product = $item->getOrderItem()->getVariant()->getProduct();
                if (($type == OrderItemInterface::TYPE_ADDON ||
                        $type == OrderItemInterface::TYPE_INCLUDED_ADDON ||
                        $type == OrderItemInterface::TYPE_ITEM) &&
                    $product->getType() == ProductInterface::TYPE_PHYSICAL
                ) {
                    if (!$item->getShipment() || !$isNeedCheckShipment) {
                        /** @var OrderItemUnit $item */
                        $orderItemUnitReturned = $this->container->get('app.service.order_item_board_type_service')->
                        isOrderItemUnitOrOrderItemReturned($item);
                        $arrayWarehouseOrderItemUnits[] = [
                            'id' => $item->getId(),
                            'product_name' => $item->getOrderItem()->getProductName(),
                            'image_required' => $item->getOrderItem() ?
                                $item->getOrderItem()->getVariant()->getImageRequired() : false,
                            'is_returned' => $orderItemUnitReturned ? true : false
                        ];
                    }
                }
            }
        }

        return $arrayWarehouseOrderItemUnits;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItemUnit;
    }
}
