<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUnitReturn;
use AppBundle\Serializer\Normalizer\OrderItemUnitsNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderItemUnitReturnNormalizer
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
        $orderItemUnits = $object;
        $checkIsOrderItemReturned = isset($context['check_is_order_item_returned']) ?
            $context['check_is_order_item_returned'] : false;

        $result = [];
        $count = 0;
        /** @var  OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $key => $orderItemUnit) {
            if (!$this->container->get("app.service.order_item_board_type_service")->isOrderItemUnitHasTypeWarranty($orderItemUnit)) {
                /** @var OrderItemUnitReturn $orderItemUnitReturned */
                $orderItemUnitReturned = $orderItemUnit->getOrderItemReturn();
                if (($orderItemUnitReturned && $checkIsOrderItemReturned)
                    ||
                    (!$orderItemUnitReturned && !$checkIsOrderItemReturned)
                ) {
                    $count++;
                    $result[$count] = (new OrderItemUnitsNormalizer())->normalize($orderItemUnit);

                    if ($checkIsOrderItemReturned) {
                        $result[$count]['date'] = $orderItemUnitReturned->getCreatedAt() ?
                            $orderItemUnitReturned->getCreatedAt()->format('Y-m-d') : "";
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItemUnitReturn;
    }
}
