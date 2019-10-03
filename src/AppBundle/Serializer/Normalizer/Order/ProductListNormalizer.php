<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Serializer\Normalizer\OrderItem\OrderItemNormalizer;
use AppBundle\Serializer\Normalizer\OrderItemIncludedAddonsNormalizer;
use AppBundle\Serializer\Normalizer\SavePriceNormalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductListNormalizer
{
    /**
     * @var OrderItemIncludedAddonsNormalizer
     */
    private $orderItemIncludedAddonsNormalizer;

    /**
     * @var OrderItemNormalizer
     */
    private $orderItemNormalizer;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ContainerAwareInterface
     */
    private $container;

    /**
     * OrderItemNormalizer constructor.
     * @param OrderItemIncludedAddonsNormalizer $orderItemIncludedAddonsNormalizer
     * @param OrderItemNormalizer $orderItemNormalizer
     * @param ObjectManager $objectManager
     * @param ContainerInterface $container
     */
    public function __construct(
        OrderItemIncludedAddonsNormalizer $orderItemIncludedAddonsNormalizer,
        OrderItemNormalizer $orderItemNormalizer,
        ObjectManager $objectManager,
        ContainerInterface $container
    ) {
        $this->orderItemIncludedAddonsNormalizer = $orderItemIncludedAddonsNormalizer;
        $this->orderItemNormalizer = $orderItemNormalizer;
        $this->objectManager = $objectManager;
        $this->container = $container;
    }

    /**
     * @param $order
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize(?Order $order, $format = null, array $context = [])
    {
        $products = [];
        $items = $order->getItems();
        $total['total'] = 0;

        /** @var OrderItem $item */
        foreach ($items as $item) {
            if ($item->getType() !== OrderItemInterface::TYPE_WARRANTY) {
                $file = $item->getProduct()->getImages()->count() > 0 ?
                    $item->getProduct()->getImages()->first()->getPath() : "";
                $image = '/media/cache/resolve/product_70_52/' . $file;
                $units = $item->getUnits();

                $warrantyCostTotal = $this->container->get("app.service.order_refund")->addWarrantyCostToItem($item);
                $warrantyCostUnit = $this->container->get("app.service.order_refund")->
                    addWarrantyCostToItem($item, true);
                $products['products'][] = [
                    'id' => $item->getId(),
                    'quantity' => $item->getQuantity(),
                    'unit_price' => $this->moneyFilter($item->getUnitPrice() + $warrantyCostUnit),
                    'total' => $this->moneyFilter($item->getTotal() + $warrantyCostTotal),
                    'discount' => 0,
                    'name' => $item->getVariantName() ?: $item->getProductName(),
                    'image' => $image,
                    'warehouse_name' => $item->getWarehouse() ? $item->getWarehouse()->getName() : "",
                    'drop_down' => $this->getDropDowns($item, $order),
                    'savePrice' => (new SavePriceNormalizer())->normalize($item->getSavePrice()),
                    'order_item_unit_refund' => $this->container->get("app.service.order_refund")->getOrderRefund($units),
                    'order_item_unit_return' => $this->container->get("app.service.order_refund")->getOrderReturn($units)
                ];

                $total['total'] = $total['total'] + $item->getTotal();
            } else {
                $total['total'] = $total['total'] + $item->getTotal();
            }
        }

        $products['total'] = $this->moneyFilter($total['total']);
        $products['discount'] = 0;

        return $products;
    }

    public function moneyFilter($value)
    {
        return number_format(($value / 100), 2, '.', '');
    }

    /**
     * Get selected dropDown
     *
     * @param OrderItem $item
     * @param Order|null $order
     * @return array
     */
    private function getDropDowns(OrderItem $item, ?Order $order)
    {
        $dropDownOptions = [];
        if ($item->getOrderItemDropDownOptions()) {
            /** @var OrderItemDropDownOption $orderItemDropDownOption */
            foreach ($item->getOrderItemDropDownOptions() as $orderItemDropDownOption) {
                /** @var DropDownOption $downOption */
                $downOption = $orderItemDropDownOption->getDropDownOption();
                $dropDownOptions[] = [
                    'id' => $orderItemDropDownOption->getId(),
                    'name' => $downOption->getDropDown()->getName() . ": " . $downOption->getName(),
                ];
            }

            if ($item->getType() == OrderItemInterface::TYPE_ITEM) {
                $repository = $this->objectManager->getRepository(OrderItem::class);
                $orderItemWarranty =
                    $repository->finWarrantyByOrderAndOrderItem($order->getId(), $item->getId());
                if (count($orderItemWarranty) > 0) {
                    $dropDownOptions[] = $this->isItemWarranty($orderItemWarranty[0]);
                }
            }
        }

        return $dropDownOptions;
    }

    /**
     * @param OrderItem|null $item
     * @return array
     */
    private function isItemWarranty(?OrderItem $item)
    {
        return [
            'id' => $item->getId(),
            'name' => "Warranty: " . $item->getProduct()->getName(),
            'price' => $item->getPrice(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Order;
    }
}
