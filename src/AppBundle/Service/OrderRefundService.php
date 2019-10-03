<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUnitRefund;
use AppBundle\Entity\OrderItemUnitReturn;
use AppBundle\Entity\OrderRefund;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderRefundService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * PayPalService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function calculateRestOrderItemUnit(?OrderItemUnit $orderIteUnit)
    {
        $orderItem = $orderIteUnit->getOrderItem();
        $orderIteUnitRefunds = $orderIteUnit->getOrderItemUnitRefund()->count() > 0 ?
            $orderIteUnit->getOrderItemUnitRefund() : [];

        $unitPrice = $orderItem->getUnitPrice();
        $unitPrice += $this->addWarrantyCostToItem($orderItem, true);

        /**
         * @var  $key
         * @var OrderItemUnitRefund $orderIteUnitRefund
         */
        foreach ($orderIteUnitRefunds as $key => $orderIteUnitRefund) {
            $unitPrice -= $orderIteUnitRefund->getTotalReturnedMoney();
        }

        return $unitPrice;
    }

    public function checkTotalRefundFromRequest($orderItemUnitsRequest, $totalRefundedCost = 0, $percent = 0)
    {
        $totalItemsCost = 0;
        $totalItemsCostFromRequest = 0;
        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnitsRequest as $orderItemUnit) {
            $totalItemsCost += $this->calculateRestOrderItemUnit($orderItemUnit['order_item_unit']);
            $totalItemsCostFromRequest += $orderItemUnit['value'];
        }

        $calculateRefundTotal = $totalItemsCost * $percent / 100;
        $minClearanceTotalRefundedCost = $totalRefundedCost - 100;
        $maxClearanceTotalRefundedCost = $totalRefundedCost + 100;

        if (($totalItemsCost >= $totalRefundedCost) &&
            ($maxClearanceTotalRefundedCost > $calculateRefundTotal) &&
            ($calculateRefundTotal > $minClearanceTotalRefundedCost)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateRefundTotal($data)
    {
        if (isset($data['order_item_unit_refund']) && count($data['order_item_unit_refund']) > 0) {
            return $this->checkTotalRefundFromRequest($data['order_item_unit_refund'], $data['total'], $data['percent']);
        }

        return true;
    }

    public function saveRefundData($data, $orderRefund)
    {
        if (isset($data['order_item_unit_refund']) && count($data['order_item_unit_refund']) > 0) {
            /** @var OrderItemUnit $orderItemUnit */
            foreach ($data['order_item_unit_refund'] as $orderItemUnit) {
                $orderItemUnitRefund = new OrderItemUnitRefund();
                $orderItemUnitRefund->setPercentOfRefund($data['percent']);
                $orderItemUnitRefund->setTotalReturnedMoney($orderItemUnit['value']);
                $orderItemUnitRefund->setOrderItemUnit($orderItemUnit['order_item_unit']);
                $orderItemUnitRefund->setOrderRefund($orderRefund);

                $this->em->persist($orderItemUnitRefund);
            }

            $this->container->get('app.service.order_board_status')->checkRefundStatuses($orderRefund->getOrder());
        }
    }

    public function saveReturnData($data, $listOrderItemUnits, $orderRefund)
    {
        if (isset($data['order_item_unit_return'])) {
            /** @var OrderItemUnit $listOrderItemUnit */
            foreach ($listOrderItemUnits as $listOrderItemUnit) {
                $orderItemUnitReturn = $listOrderItemUnit->getOrderItemReturn();

                $needToRemove = true;
                foreach ($data['order_item_unit_return'] as $returnedOrderItemUnit) {
                    if ($orderItemUnitReturn && $listOrderItemUnit === $returnedOrderItemUnit) {
                        $needToRemove = false;
                        break;
                    }
                }

                if ($needToRemove && $orderItemUnitReturn) {
                    $this->em->remove($orderItemUnitReturn);
                    $listOrderItemUnit->setOrderItemReturn(null);
                }

                if (!$orderItemUnitReturn) {
                    $needToCreate = false;
                    foreach ($data['order_item_unit_return'] as $returnedOrderItemUnit) {
                        if (!$orderItemUnitReturn && $listOrderItemUnit === $returnedOrderItemUnit) {
                            $needToCreate = true;
                            break;
                        }
                    }
                    if ($needToCreate) {
                        $returnedUnit = new OrderItemUnitReturn();
                        $returnedUnit->setOrderItemUnit($listOrderItemUnit);
                        $returnedUnit->setOrderRefund($orderRefund);
                        $returnedUnit->setCreatedAt(new \DateTime());
                        $listOrderItemUnit->setOrderItemReturn($returnedUnit);

                        $this->em->persist($returnedUnit);
                    }
                }
            }

            $this->em->flush();

            $this->container->get('app.service.order_board_status')->
                checkReturnedStatuses($data['order_id'], $listOrderItemUnits);
        }
    }

    public function createOrderRefund($data, $isCreate = true)
    {
        if ($isCreate) {
            $orderRefund = new OrderRefund();
            $orderRefund->setCreatedAt(new \DateTime('now'));
            if (isset($data['order_item_unit_refund']) && count($data['order_item_unit_refund']) > 0) {
                $orderRefund->setTotalReturnedMoney($data['total']);
                $orderRefund->setPercentOfRefund($data['percent']);
            }
            $data['order_id']->addOrderRefund($orderRefund);
        } else {
            $orderRefund = $data['order_refund_id'];
        }
        $orderRefund->setComment($data["comment"]);
        $orderRefund->setOrder($data['order_id']);

        $this->em->persist($orderRefund);

        return $orderRefund;
    }

    public function getRefundedTotalFromOrder($order)
    {
        /** @var Order $order */
        $refunds = $order->getOrderRefund();
        $totalRefunded = 0;
        /** @var OrderRefund $refund */
        foreach ($refunds as $refund) {
            $totalRefunded += $refund->getTotalReturnedMoney();
        }

        return $totalRefunded;
    }

    public function getItemRefundAndReturn(?OrderItem $orderItem)
    {
        $units = $orderItem->getUnits();
    }

    public function getOrderRefund($units)
    {
        $refund = [];
        /** @var OrderItemUnit $unit */
        foreach ($units as $unit) {
            $orderItemUnitRefunds = $unit->getOrderItemUnitRefund();
            /** @var OrderItemUnitRefund $orderItemUnitRefund */
            foreach ($orderItemUnitRefunds as $orderItemUnitRefund) {
                $orderRefund = $orderItemUnitRefund->getOrderRefund();
                $refund[] = $this->container->get("app.normalizer.order_refund")->normalize($orderRefund);
            }
        }

        return $refund;
    }

    public function getOrderReturn($units)
    {
        $returned = [];
        /** @var OrderItemUnit $unit */
        foreach ($units as $unit) {
            $orderItemUnitReturn = $unit->getOrderItemReturn();
            if ($orderItemUnitReturn) {
                $orderRefund = $orderItemUnitReturn->getOrderRefund();
                $returned[] = $this->container->get("app.normalizer.order_refund")->normalize($orderRefund);
            }

        }

        return $returned;
    }

    public function addWarrantyCostToItem(?OrderItem $item, $isUnitCost = false)
    {
        $warrantyCost = 0;
        if ($item->getType() == OrderItemInterface::TYPE_ITEM) {
            $order = $item->getOrder();
            $repository = $this->em->getRepository(OrderItem::class);
            $orderItemWarranties =
                $repository->finWarrantyByOrderAndOrderItem($order->getId(), $item->getId());
            if (count($orderItemWarranties) > 0) {
                /** @var OrderItem $orderItemWarranty */
                foreach ($orderItemWarranties as $orderItemWarranty) {
                    if (!$isUnitCost) {
                        $warrantyCost += $orderItemWarranty->getTotal();
                    } elseif ($isUnitCost) {
                        $warrantyCost += $orderItemWarranty->getUnitPrice();
                    }
                }
            }
        }

        return $warrantyCost;
    }
}
