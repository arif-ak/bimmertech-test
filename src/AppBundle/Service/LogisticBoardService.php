<?php

namespace AppBundle\Service;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Warehouse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class LogisticBoardService
 * @package AppBundle\Service
 */
class LogisticBoardService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * LogisticBoardService constructor.
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container
    ) {
        $this->em = $em;
        $this->container = $container;
    }

    public function warehouseOrderItems(Order $order)
    {
        $warehouseTable = [];

        $orderItemUnits = $this->em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($order->getId());

        $orderItems =$order->getItems();

        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            $warehouse = $orderItemUnit->getWarehouse();

            if ($warehouse) {
                if (!$this->container->get("app.service.order_item_board_type_service")
                    ->isOrderItemUnitHasTypeWarranty($orderItemUnit)) {
                    if ($this->container->get('app.service.order_item_board_type_service')->
                        checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($orderItemUnit)) {
                        $key = $this->inWhatAdd($warehouseTable, $warehouse);
                        if (!isset($warehouseTable[$key]['order_item_units'])) {
                            $warehouseTable[$key]['order_item_units'] = [];
                        }

                        array_push($warehouseTable[$key]['order_item_units'], $orderItemUnit);
                    }
                }
            }
        }

        foreach ($orderItems as $itemKey => $orderItem) {
            $warehouse = $orderItem->getWarehouse();

            if ($warehouse) {
                if ($this->container->get('app.service.order_item_board_type_service')
                    ->checkIsOrderItemUnitIsUsbCoding($orderItem)) {
                    $key = $this->inWhatAdd($warehouseTable, $warehouse);
                    if (!isset($warehouseTable[$key]['order_item_usb_coding'])) {
                        $warehouseTable[$key]['order_item_usb_coding'] = [];
                        $warehouseTable[$key]['product_usb_coding'] = [];
                    }

                    array_push($warehouseTable[$key]['order_item_usb_coding'], $orderItem);
                    array_push($warehouseTable[$key]['product_usb_coding'], $orderItem);
                }
            }
        }

        $this->getProducts($warehouseTable, $order);

        return $warehouseTable;
    }

    /**
     * @param $warehouseTable
     * @param Order $order
     */
    private function getProducts(&$warehouseTable, $order)
    {
        foreach ($warehouseTable as &$tableItem) {
            $warehouseUnit = [];
            $tableItem['vin'] = $order->getVin();

            if (isset($tableItem['order_item_units']) && count($tableItem['order_item_units']) > 0) {
                /** @var OrderItemUnit $itemUnit */
                foreach ($tableItem['order_item_units'] as $key => $itemUnit) {
                    $itemId = $itemUnit->getOrderItem()->getId();
                    if (!isset($tableItem['products'][$itemId]['product'])) {
                        $tableItem['products'][$itemId]['product'] = $itemUnit->getOrderItem();

                        if ($itemUnit->getShipment()) {
                            $tableItem['products'][$itemId]['count'] = 0;
                            $tableItem['products'][$itemId]['shipment'][$itemUnit->getShipment()->getId()] =
                                $itemUnit->getShipment();
                        } else {
                            $tableItem['products'][$itemId]['count'] = 1;
                            $tableItem['products'][$itemId]['shipment'] = null;
                        }
                    } else {
                        if (!$itemUnit->getShipment()) {
                            $tableItem['products'][$itemId]['count'] =
                                $tableItem['products'][$itemId]['count'] + 1;
                        } else {
                            $tableItem['products'][$itemId]['shipment'][$itemUnit->getShipment()->getId()] =
                                $itemUnit->getShipment();
                            $tableItem['products'][$itemId]['shipment'][$itemUnit->getShipment()->getId()] =
                                $itemUnit->getShipment();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $warehouseTable
     * @param Warehouse $itemWarehouse
     * @return int
     */
    private function inWhatAdd(array &$warehouseTable, Warehouse $itemWarehouse)
    {
        foreach ($warehouseTable as $key => $warehouse) {
            if ($warehouse['warehouse'] === $itemWarehouse) {
                return $key;
            }
        }
        $newWarehouse = ['warehouse' => $itemWarehouse];
        array_push($warehouseTable, $newWarehouse);

        return count($warehouseTable) - 1;
    }
}
