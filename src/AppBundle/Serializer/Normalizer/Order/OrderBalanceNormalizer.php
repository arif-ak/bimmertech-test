<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUnitRefund;
use AppBundle\Entity\OrderItemUnitReturn;
use AppBundle\Serializer\Normalizer\OrderItemUnitsNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderBalanceNormalizer
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
        $orderIteUnits = $object;
        $result = [];
        $orderBalance = 0;
        /**
         * @var  $key
         * @var OrderItemUnit $orderIteUnit
         */
        foreach ($orderIteUnits as $key => $orderIteUnit) {
            if (!$this->container->get("app.service.order_item_board_type_service")->isOrderItemUnitHasTypeWarranty($orderIteUnit)) {
                $result['order_item_unit_balance'][$key] = (new OrderItemUnitsNormalizer())->normalize($orderIteUnit);
                $unitBalance = $this->container->get('app.service.order_refund')
                    ->calculateRestOrderItemUnit($orderIteUnit);

                $result['order_item_unit_balance'][$key]['rest_of_balance'] = $unitBalance;
                $orderBalance += $unitBalance;

                /** @var OrderItemUnitReturn $orderItemUnitReturned */
                $orderItemUnitReturned = $orderIteUnit->getOrderItemReturn();
                $result['order_item_unit_balance'][$key]["is_returned"] = false;
                if ($orderItemUnitReturned) {
                    $result['order_item_unit_balance'][$key]["is_returned"] = true;
                }
            }
        }

        $result['order_balance'] = $orderBalance;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItemUnit;
    }
}
