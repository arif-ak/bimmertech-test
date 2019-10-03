<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\PaymentInterface;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;

class OrderBoardStatusService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Container
     */
    private $container;

    /**
     * ElasticSearchService constructor.
     *
     * @param EntityManagerInterface $em
     * @param Container $container
     */
    public function __construct(
        EntityManagerInterface $em,
        Container $container
    )
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param Order $order
     * @param Shipment $shipment
     */
    public function checkShippingStatus($order, $shipment = null)
    {
        $shippingCompliedArray = [];
        $orderItemUnits =
            $this->em->getRepository(OrderItemUnit::class)->getByShipmentAndWarehouse($order->getId());
        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            if ($this->container->get('app.service.order_item_board_type_service')->
                checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($orderItemUnit) &&
                !$this->container->get('app.service.order_item_board_type_service')
                    ->isOrderItemUnitOrOrderItemReturned($orderItemUnit)) {
                if ($orderItemUnit->getShipment() && !empty($orderItemUnit->getShipment()->getTracking())) {
                    $shippingCompliedArray[] = ShipmentInterface::STATE_SHIPPED;
                } else {
                    $shippingCompliedArray[] = ShipmentInterface::STATE_NOT_SHIPPED;
                }
            }
        }

        if (in_array(ShipmentInterface::STATE_SHIPPED, $shippingCompliedArray) &&
            !in_array(ShipmentInterface::STATE_NOT_SHIPPED, $shippingCompliedArray)) {
            $order->setShippingState(ShipmentInterface::STATE_SHIPPED);
        } elseif (in_array(ShipmentInterface::STATE_SHIPPED, $shippingCompliedArray) &&
            in_array(ShipmentInterface::STATE_NOT_SHIPPED, $shippingCompliedArray)) {
            $order->setShippingState(ShipmentInterface::STATE_PARTIALLY_SHIPPED);
        } elseif (in_array(ShipmentInterface::STATE_NOT_SHIPPED, $shippingCompliedArray) &&
            !in_array(ShipmentInterface::STATE_SHIPPED, $shippingCompliedArray)) {
            $order->setShippingState(ShipmentInterface::STATE_READY);
        }

        if ($shipment) {
            $shipment->setState(Shipment::STATE_SHIPPED);
        }

        $this->checkOrderStatus($order);
    }

    /**
     * @param $order
     * @param bool $checkGeneralOrderStatus
     * @throws \Exception
     */
    public function checkUsbCodingBoardStatus($order, $checkGeneralOrderStatus = false)
    {
        /** @var Order $order */
        $items = $order->getItems();

        $usbCoddingCompliedArray = [];

        /** @var OrderItem $item */
        foreach ($items as $item) {
            if ($this->container->get('app.service.order_item_board_type_service')->
                checkIsOrderItemUnitIsUsbCoding($item) &&
                !$this->container->get('app.service.order_item_board_type_service')->
                isOrderItemUnitOrOrderItemReturned($item)
            ) {
                $orderItemUsbCoding = $item->getOrderItemUsbCoding();
                if ($orderItemUsbCoding) {
                    $usbCoddingCompliedArray[] = OrderItem::COMPLETE;
                } else {
                    $usbCoddingCompliedArray[] = OrderItem::NOT_CODED;
                }
            }
        }

        if (in_array(OrderItem::COMPLETE, $usbCoddingCompliedArray) &&
            !in_array(OrderItem::NOT_CODED, $usbCoddingCompliedArray)) {
            $order->setUsbCodingStatus(OrderItemInterface::COMPLETE);
            $this->updateOrderStatusForCoding($order);
        } elseif (in_array(OrderItem::NOT_CODED, $usbCoddingCompliedArray) &&
            in_array(OrderItem::COMPLETE, $usbCoddingCompliedArray)) {
            $order->setUsbCodingStatus(OrderItemInterface::PARTIALLY_CODED);
            $this->updateOrderStatusForCoding($order);
        } elseif (in_array(OrderItem::NOT_CODED, $usbCoddingCompliedArray) &&
            !in_array(OrderItem::COMPLETE, $usbCoddingCompliedArray)) {
            $order->setUsbCodingStatus(OrderItemInterface::NOT_CODED);
            if ($checkGeneralOrderStatus) {
                $order->setState(OrderInterface::STATUS_PLACED);
                $this->checkIsShippingDeliveredStatus($order);
                $this->checkCodingStatuses($order);
            }
        }

        $this->checkOrderStatus($order);
    }

    /**
     * @param $order
     */
    public function checkSupportStatuses($order)
    {
        /** @var Order $order */
        $items = $order->getItems();

        $supportCompliedArray = [];

        /** @var OrderItem $item */
        foreach ($items as $item) {
            /** @var ProductVariant $productVariant */
            $productVariant = $item->getVariant();
            $product = $item->getProduct();
            if ($this->container->get('app.service.order_item_board_type_service')->
                isOrderItemHasRequireInstruction($item) &&
                !$this->container->get('app.service.order_item_board_type_service')
                    ->isOrderItemUnitOrOrderItemReturned($item)
            ) {
                if ($item->getSupportStatus() == OrderItemInterface::COMPLETE) {
                    $supportCompliedArray[] = OrderItemInterface::COMPLETE;
                } else {
                    $supportCompliedArray[] = OrderItemInterface::NOT_ADDED;
                }
            }
        }

        if (in_array(OrderItemInterface::COMPLETE, $supportCompliedArray) &&
            !in_array(OrderItemInterface::NOT_ADDED, $supportCompliedArray)) {
            $order->setSupportStatus(OrderItemInterface::COMPLETE);
        } elseif (in_array(OrderItemInterface::NOT_ADDED, $supportCompliedArray) &&
            in_array(OrderItemInterface::COMPLETE, $supportCompliedArray)) {
            $order->setSupportStatus(OrderItemInterface::PARTIALLY_ADDED);
        } elseif (in_array(OrderItemInterface::NOT_ADDED, $supportCompliedArray) &&
            !in_array(OrderItemInterface::COMPLETE, $supportCompliedArray)) {
            $order->setSupportStatus(OrderItemInterface::NOT_ADDED);
        }

        $this->checkOrderStatus($order);

        return $order;
    }

    /**
     * @param $order
     * @param bool $checkGeneralOrderStatus
     * @throws \Exception
     */
    public function checkCodingStatuses($order, $checkGeneralOrderStatus = false)
    {
        /** @var Order $order */
        $items = $order->getItems();

        $coddingCompliedArray = [];

        /** @var OrderItem $item */
        foreach ($items as $item) {
            if ($this->container->get('app.service.order_item_board_type_service')->isCoddingItem($item) &&
                !$this->container->get('app.service.order_item_board_type_service')->
                isOrderItemUnitOrOrderItemReturned($item)) {
                if ($item->getCodingStatus() == OrderItemInterface::COMPLETE) {
                    $coddingCompliedArray[] = OrderItem::COMPLETE;
                } else {
                    $coddingCompliedArray[] = OrderItem::NOT_CODED;
                }
            }
        }

        if (in_array(OrderItem::COMPLETE, $coddingCompliedArray) &&
            !in_array(OrderItem::NOT_CODED, $coddingCompliedArray)) {
            $order->setCodingStatus(OrderItemInterface::COMPLETE);
            $this->updateOrderStatusForCoding($order);
        } elseif (in_array(OrderItem::NOT_CODED, $coddingCompliedArray) &&
            in_array(OrderItem::COMPLETE, $coddingCompliedArray)) {
            $order->setCodingStatus(OrderItemInterface::PARTIALLY_CODED);
            $this->updateOrderStatusForCoding($order);
        } elseif (in_array(OrderItem::NOT_CODED, $coddingCompliedArray) &&
            !in_array(OrderItem::COMPLETE, $coddingCompliedArray)) {
            $order->setCodingStatus(OrderItemInterface::NOT_CODED);

            if ($checkGeneralOrderStatus) {
                $order->setState(OrderInterface::STATUS_PLACED);
                $this->checkIsShippingDeliveredStatus($order);
                $this->checkUsbCodingBoardStatus($order);
            }
        }

        $this->checkOrderStatus($order);
    }

    /**
     * @param Order $order
     */
    public function checkOrderStatus($order)
    {
        if (($order->getCodingStatus() == OrderItemInterface::COMPLETE ||
                $order->getCodingStatus() == OrderItemInterface::NOT_REQUIRED) &&
            ($order->getSupportStatus() == OrderItemInterface::COMPLETE ||
                $order->getSupportStatus() == OrderItemInterface::NOT_REQUIRED) &&
            ($order->getShippingState() == Shipment::STATE_DELIVERED ||
                !$this->container->get('app.service.order_item_board_type_service')->isOrderHasShipment($order)) &&
            ($order->getPaymentState() == PaymentInterface::STATE_COMPLETED ||
                $order->getPaymentState() == PaymentInterface::STATE_PARTIALLY_REFUNDED ||
                $order->getPaymentState() == PaymentInterface::STATE_PAID) &&
            ($order->getUsbCodingStatus() == OrderItemInterface::COMPLETE ||
                $order->getUsbCodingStatus() == OrderItemInterface::NOT_REQUIRED)
        ) {
            if ($order->getState() !== Order::STATE_CANCELLED) {
                $order->setState(Order::STATE_FULFILLED);
            }
        }
    }

    public function checkIsShippingDeliveredStatus($order, $shipment = null)
    {
        $shippingCompliedArray = [];
        $orderItemUnits =
            $this->em->getRepository(OrderItemUnit::class)->getByShipmentAndWarehouse($order->getId());
        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            if ($this->container->get('app.service.order_item_board_type_service')->
                checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($orderItemUnit) &&
                !$this->container->get('app.service.order_item_board_type_service')
                    ->isOrderItemUnitOrOrderItemReturned($orderItemUnit)) {
                if ($orderItemUnit->getShipment() &&
                    !empty($orderItemUnit->getShipment()->getTracking()) &&
                    $orderItemUnit->getShipment()->getState() == ShipmentInterface::STATE_DELIVERED
                ) {
                    $shippingCompliedArray[] = ShipmentInterface::STATE_DELIVERED;
                } else {
                    $shippingCompliedArray[] = ShipmentInterface::STATE_NOT_SHIPPED;
                }
            }
        }

        if (in_array(ShipmentInterface::STATE_DELIVERED, $shippingCompliedArray) &&
            !in_array(ShipmentInterface::STATE_NOT_SHIPPED, $shippingCompliedArray)) {
            $order->setShippingState(ShipmentInterface::STATE_DELIVERED);
            $this->updateOrderStatusForCoding($order);
        } elseif (in_array(ShipmentInterface::STATE_DELIVERED, $shippingCompliedArray) &&
            in_array(ShipmentInterface::STATE_NOT_SHIPPED, $shippingCompliedArray)) {
            $this->updateOrderStatusForCoding($order);
        }

        if ($shipment) {
            $shipment->setState(Shipment::STATE_SHIPPED);
        }

        $this->checkOrderStatus($order);
    }

    public function checkReturnedStatuses(?Order $order, $orderItemUnits)
    {
        $returnedCompliedArray = [];

        /** @var OrderItem $item */
        foreach ($orderItemUnits as $item) {
            if (!$this->container->get('app.service.order_item_board_type_service')->
            isOrderItemUnitHasTypeWarranty($item)) {
                if ($this->container->get('app.service.order_item_board_type_service')->
                isOrderItemUnitOrOrderItemReturned($item)
                ) {
                    $returnedCompliedArray[] = OrderItem::COMPLETE;
                } else {
                    $returnedCompliedArray[] = OrderItem::NOT_CODED;
                }
            }
        }

        if (in_array(OrderItem::COMPLETE, $returnedCompliedArray) &&
            !in_array(OrderItem::NOT_CODED, $returnedCompliedArray)) {
            $this->setOrderReturnedStatus($order, true);
        } elseif (in_array(OrderItem::NOT_CODED, $returnedCompliedArray) &&
            in_array(OrderItem::COMPLETE, $returnedCompliedArray)) {
            $this->setOrderReturnedStatus($order);
        } elseif (in_array(OrderItem::NOT_CODED, $returnedCompliedArray) &&
            !in_array(OrderItem::COMPLETE, $returnedCompliedArray)) {
                $order->setState(OrderInterface::STATUS_PLACED);
                $this->checkIsShippingDeliveredStatus($order);
                $this->checkUsbCodingBoardStatus($order);
                $this->checkCodingStatuses($order);

        }

        $this->checkOrderStatus($order);
    }

    public function checkRefundStatuses(?Order $order)
    {
        $refundTotal = $this->container->get('app.service.order_refund')->getRefundedTotalFromOrder($order);

        $orderTotal = $order->getTotal();

        if ($refundTotal == $orderTotal) {
            $order->setPaymentState(PaymentInterface::STATE_REFUNDED);
            $order->setState(Order::STATE_CANCELLED);
        } elseif ($orderTotal > $refundTotal && $refundTotal >= 0) {
            $order->setPaymentState(PaymentInterface::STATE_PARTIALLY_REFUNDED);
        }
    }

    public function setOrderReturnedStatus($order, $isFullReturned = false)
    {
        /** @var Order $order */
        $orderStatus = $order->getState();
        if ($orderStatus == Order::STATUS_NEW ||
            $orderStatus == Order::STATUS_PLACED ||
            $orderStatus == Order::STATE_CART ||
            $orderStatus == Order::STATUS_PARTIALLY_FULFILLED ||
            $orderStatus == Order::STATUS_PARTIALLY_RETURNED
        ) {
            if (!$isFullReturned) {
                $order->setState(Order::STATUS_PARTIALLY_RETURNED);
            } elseif ($isFullReturned) {
                $order->setState(Order::STATUS_RETURNED);
            }
        }
    }

    public function updateOrderStatusForCoding($order)
    {
        /** @var Order $order */
        $orderStatus = $order->getState();
        if ($orderStatus == Order::STATUS_NEW ||
            $orderStatus == Order::STATUS_PLACED ||
            $orderStatus == Order::STATE_CART
        ) {
            $order->setState(Order::STATUS_PARTIALLY_FULFILLED);
        }
    }
}
