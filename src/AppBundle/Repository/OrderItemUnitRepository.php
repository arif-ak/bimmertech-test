<?php

namespace AppBundle\Repository;

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository as BaseOrderItemRepository;

/**
 * Class OrderRepository
 *
 * @package AppBundle\Repository
 */
class OrderItemUnitRepository extends BaseOrderItemRepository
{
    /**
     * @param $orderId
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByShipmentAndWarehouse($orderId, $params = [])
    {
        $orderItemUnits = $this->createQueryBuilder('oiu')
            ->innerJoin("oiu.orderItem", "oi")
            ->andWhere("oi.order = :orderId")
            ->setParameter('orderId', $orderId);

        if (isset($params["shipmentId"])) {
            $orderItemUnits
                ->andWhere("oiu.shipment_id = :shipmentId")
                ->setParameter("shipmentId", $params["shipmentId"]);
        }

        if (isset($params["warehouseId"])) {
            $orderItemUnits
                ->andWhere("oiu.warehouse_id = :warehouseId")
                ->setParameter("warehouseId", $params["warehouseId"]);
        }

        return $orderItemUnits
            ->getQuery()
            ->getResult();
    }
}
