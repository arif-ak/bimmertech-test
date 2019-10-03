<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUnitRefund;
use AppBundle\Entity\OrderItemUnitReturn;
use AppBundle\Entity\OrderRefund;
use AppBundle\Serializer\Normalizer\OrderItemUnitsNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderRefundNormalizer
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
     * OrderBalanceNormalizer constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $showItems = isset($context['showItem']) ? $context['showItem'] : false;

        /** @var OrderRefund $orderRefund */
        $orderRefund = $object;

        $result = [
            "id" => $orderRefund->getId(),
            "total" => $orderRefund->getTotalReturnedMoney() ? $orderRefund->getTotalReturnedMoney() : 0,
            "percent" => $orderRefund->getPercentOfRefund() ? $orderRefund->getPercentOfRefund() : 0,
            "comment" => $orderRefund->getComment() ? $orderRefund->getComment() : "",
            "date" => $orderRefund->getCreatedAt() ? $orderRefund->getCreatedAt()->format("Y-m-d") : "",
        ];

        if ($showItems) {
            $result['order_unit_items'] = $this->orderItemUnitRefund($context['order_item_units'], $orderRefund);
        }

        return $result;
    }

    public function orderItemUnitRefund($orderItemUnits, ?OrderRefund $orderRefund)
    {
        $data = [];

        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $key => $orderItemUnit) {
            if (!$this->container->get("app.service.order_item_board_type_service")->isOrderItemUnitHasTypeWarranty($orderItemUnit)) {
                $orderItemUnitRefunds = $orderItemUnit->getOrderItemUnitRefund()->count() > 0 ?
                    $orderItemUnit->getOrderItemUnitRefund() : [];
                /** @var OrderItemUnitRefund $orderItemUnitRefund */

                $isRefunded = false;
                foreach ($orderItemUnitRefunds as $orderItemUnitRefund) {
                    $orderUnitRefund = $orderItemUnitRefund->getOrderRefund();
                    if ($orderUnitRefund && $orderUnitRefund === $orderRefund) {
                        $isRefunded = true;
                    }
                }

                /** @var OrderItemUnitReturn $orderItemUnitReturned */
                $orderItemUnitReturned = $orderItemUnit->getOrderItemReturn();
                if ($isRefunded) {
                    $data['order_item_units_free'][$key] = (new OrderItemUnitsNormalizer())->normalize($orderItemUnit);
                    $data['order_item_units_free'][$key]["is_returned"] = $orderItemUnitReturned ? true : false;
                } else {
                    $data['order_item_units_refund'][$key] =
                        (new OrderItemUnitsNormalizer())->normalize($orderItemUnit);
                    $data['order_item_units_refund'][$key]["is_returned"] = $orderItemUnitReturned ? true : false;
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderRefund;
    }
}
