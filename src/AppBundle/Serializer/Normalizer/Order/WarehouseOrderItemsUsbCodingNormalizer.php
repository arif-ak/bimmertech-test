<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WarehouseOrderItemsUsbCodingNormalizer
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
        $checkIsUsbCodingSent = isset($context['check_is_usb_coding_sent']) ? $context['check_is_usb_coding_sent'] : false;

        $arrayWarehouseOrderItems = [];
        $warehouseOrderItems = $object;

        /** @var Order $orderItem */
        foreach ($warehouseOrderItems as $orderItem) {
            /** @var OrderItem $orderItem */
            $type = $orderItem->getType();

            /** @var Product $product */
            $product = $orderItem->getVariant()->getProduct();
            $usbCoding = $orderItem->getOrderItemUsbCoding();

            if ($this->container->get('app.service.order_item_board_type_service')->
                checkIsOrderItemUnitIsUsbCoding($orderItem)
            ) {
                /** @var OrderItemUnit $item */
                $orderItemUnitReturned = $this->container->get('app.service.order_item_board_type_service')->
                isOrderItemUnitOrOrderItemReturned($orderItem);
                if ($checkIsUsbCodingSent) {
                    if ($usbCoding) {
                        $arrayWarehouseOrderItems[] = [
                            'id' => $orderItem->getId(),
                            'product_name' => $orderItem->getProductName(),
                            'image_required' => $orderItem ?
                                $orderItem->getVariant()->getImageRequired() : false,
                            'is_returned' => $orderItemUnitReturned ? true : false
                        ];
                    }
                } else {
                    if (!$usbCoding) {
                        $arrayWarehouseOrderItems[] = [
                            'id' => $orderItem->getId(),
                            'product_name' => $orderItem->getProductName(),
                            'image_required' => $orderItem ?
                                $orderItem->getVariant()->getImageRequired() : false,
                            'is_returned' => $orderItemUnitReturned ? true : false
                        ];
                    }
                }
            }
        }

        return $arrayWarehouseOrderItems;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItem;
    }
}
