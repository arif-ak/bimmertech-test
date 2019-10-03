<?php

namespace AppBundle\Repository;

use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\Product;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository as BaseOrderItemRepository;
use Sylius\Component\Core\Model\CustomerInterface;


/**
 * Class OrderRepository
 *
 * @package AppBundle\Repository
 */
class OrderItemRepository extends BaseOrderItemRepository
{
    /**
     * @param $variantId
     * @param $orderId
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOrderItemByVariantByOrder($variantId, $orderId)
    {

        $orderItem = $this->createQueryBuilder('orderItem')
            ->where('orderItem.variant = :variantId')
            ->andWhere('orderItem.order = :orderId')
            ->setParameter('variantId', $variantId)
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult();

        if (count($orderItem) > 0) {
            return $orderItem[0];
        }
        return null;
    }

    /**
     * Get customer's items fo coding bord
     *
     * @param CustomerInterface $customer
     * @return mixed
     */
    public function getCodingByCustomer(CustomerInterface $customer){
        return $this->createQueryBuilder('i')
            ->where('i.supportStatus != :status')
            ->orWhere('i.codingStatus != :status')
            ->innerJoin(
                'i.order',
                'o',
                'WITH',
                ' o.paymentState = :payment AND o.customer = :customer '
            )
            ->setParameter('customer', $customer)
            ->setParameter('status', 'not required')
            ->setParameter('payment','completed')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $product
     * @return array
     */
    public function finWhereUsedProduct( Product $product):array
    {
        $orderItems = [];
        $result = $this->createQueryBuilder('orderItem')
            ->join('orderItem.variant', 'variant')
            ->andWhere('variant = :variant')
            ->setParameter('variant', $product->getVariants()->first())
            ->getQuery()
            ->getResult();
        /** @var ProductAssociation $item */
        foreach ($result as $item) {
            $orderItems [] = $item;
        }
        return $orderItems;
    }

    /**
     * @param $orderId
     * @param $orderItemId
     * @return array
     */
    public function finWarrantyByOrderAndOrderItem($orderId, $orderItemId):array
    {
        return $this->createQueryBuilder('oi')
            ->andWhere('oi.order = :order')
            ->andWhere('oi.parent = :orderItem')
            ->andWhere('oi.type = :type')
            ->setParameter('order', $orderId)
            ->setParameter('orderItem', $orderItemId)
            ->setParameter('type', OrderItemInterface::TYPE_WARRANTY)
            ->getQuery()
            ->getResult();
    }
}
