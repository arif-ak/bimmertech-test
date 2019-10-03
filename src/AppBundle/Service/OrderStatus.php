<?php

namespace AppBundle\Service;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Shipment;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrderStatus
 * @package AppBundle\Service
 */
class OrderStatus
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
     * Update statuses for order
     *
     * @param  Order $object
     * @return mixed
     */
    public function update($object)
    {
        // update support status
        $order = $this->updateSupport($object);
        // update codding status
        $order = $this->updateCodding($order);
        // update shipment status
        $order = $this->updateShipping($order);
        // update usb codding status
        $order = $this->updateUsbCoding($order);

        return $order;
    }

    /**
     * @param Order $object
     * @return mixed
     */
    private function updateCodding($object)
    {
        $coddingNotRequired = true;
        /** @var OrderItem $item */
        foreach ($object->getItems() as $item) {
            if ($this->container->get('app.service.order_item_board_type_service')->isCoddingItem($item)) {
                $object->setCodingStatus($item::NOT_CODED);
                $item->setCodingStatus($item::NOT_CODED);
                $coddingNotRequired = false;
            }
        }

        if ($coddingNotRequired) {
            $object->setCodingStatus(OrderItemInterface::NOT_REQUIRED);
        }

        return $object;
    }

    /**
     * @param Order $object
     * @return mixed
     */
    private function updateSupport($object)
    {
        $instructionNotRequired = true;
        /** @var OrderItem $item */
        foreach ($object->getItems() as $item) {

            /** @var ProductVariant $variant */
            $variant = $item->getVariant();

            if ($this->container->get('app.service.order_item_board_type_service')->
            isOrderItemHasRequireInstruction($item)) {
                $object->setSupportStatus($item::NOT_ADDED);
                $item->setSupportStatus($item::NOT_ADDED);
                $instructionNotRequired = false;
            }
        }

        if ($instructionNotRequired) {
            $object->setSupportStatus(OrderItemInterface::NOT_REQUIRED);
        }

        return $object;
    }

    /**
     * @param Order $object
     * @return mixed
     */
    private function updateUsbCoding($object)
    {
        $instructionIsUsbRequired = true;
        /** @var OrderItem $item */
        foreach ($object->getItems() as $item) {

            /** @var ProductVariant $variant */
            $product = $item->getProduct();
            if ($this->container->get('app.service.order_item_board_type_service')
                ->checkIsOrderItemUnitIsUsbCoding($item)) {
                $object->setUsbCodingStatus($item::NOT_CODED);
                $instructionIsUsbRequired = false;
            }
        }

        if ($instructionIsUsbRequired) {
            $object->setUsbCodingStatus(OrderItemInterface::NOT_REQUIRED);
        }

        return $object;
    }

    /**
     * @param Order $order
     * @return mixed
     */
    private function updateShipping($order)
    {
        $order->setShippingState(Shipment::STATE_NOT_SHIPPED);

        return $order;
    }
}
