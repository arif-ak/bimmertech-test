<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShippingMethod;
use AppBundle\Entity\Warehouse;
use AppBundle\Serializer\Normalizer\SavePriceNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WarehouseOrderItemsNormalizer
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

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $warehouseOrderItems = [];
        foreach ($object as $warehouse) {
            $warehouseName = isset($warehouse['warehouse']) ? $warehouse['warehouse']->getName() : null;

            $warehouseOrderItems[$warehouseName]['vin'] = isset($warehouse['vin']) ? $warehouse['vin'] : null;

            $warehouseOrderItems[$warehouseName]['order_item_units'] = $this->container->
            get('app.serializer_normalizer.warehouse_order_item_unit_normalizer')->
                normalize(isset($warehouse['order_item_units']) ? $warehouse['order_item_units'] : [], null, ['checkShipment' => true]);

            $warehouseOrderItems[$warehouseName]['warehouse'] =
                count($warehouse['warehouse']) > 0 ? $this->warehouse($warehouse['warehouse']) : [];

            $warehouseOrderItems[$warehouseName]['shippingMethods'] =
                count($warehouse['warehouse']) > 0 ? $this->getShippingMethods($warehouse['warehouse']) : [];

            $warehouseOrderItems[$warehouseName]['shipments'] = isset($warehouse['products']) ?
                $this->getShipments($warehouse['products']) : [];

            if (in_array($warehouseName, $context['logistic_board_access'])) {
                $warehouseOrderItems[$warehouseName]['logistic_board_access'] = true;
            } else {
                $warehouseOrderItems[$warehouseName]['logistic_board_access'] = false;
            }

            if (in_array($warehouseName, $context['usb_coding_board_access'])) {
                $warehouseOrderItems[$warehouseName]['usb_coding_board_access'] = true;
            } else {
                $warehouseOrderItems[$warehouseName]['usb_coding_board_access'] = false;
            }

            $vin = isset($context['vin']) ? $context['vin'] : null;

            if (isset($warehouse['products'])) {
                $counter = 0;
                foreach ($warehouse['products'] as $key => $value) {
                    /** @var OrderItem $orderItem */
                    $orderItem = $value['product'];
                    /** @var Product $product */
                    $productVariant = $orderItem->getVariant();
                    $product = $productVariant->getProduct();
                    $vinId = $product->getVinCheckProductId();
                    $id = $orderItem->getId();
                    $units = $orderItem->getUnits();

                    $image = '/images/no-image.png';
                    if ($product->getImages()->count()) {
                        $image = '/media/cache/resolve/logistic_bord_78_50/' .
                            $product->getImages()->first()->getPath();
                    }

                    $warehouseOrderItems[$warehouseName]['products'][$counter] = [
                        'id' => $id,
                        'quantity' => $value['count'],
                        'productName' => $orderItem ? $orderItem->getProductName() : "",
                        'productCode' => $product ? $product->getCode() : "",
                        'productImage' => $image,
                        'bopt' => "/bopt?vin=$vin&productId=$vinId",
                        'savePrice' => (new SavePriceNormalizer())->normalize($orderItem->getSavePrice()),
                        'order_item_unit_refund' => $this->container->get("app.service.order_refund")->getOrderRefund($units),
                        'order_item_unit_return' => $this->container->get("app.service.order_refund")->getOrderReturn($units)
                    ];

                    $shipments = isset($value['shipment']) ? $value['shipment'] : null;

                    $warehouseOrderItems[$warehouseName]['products'][$counter]['shipment'] = isset($value['shipment']) ?
                        (new WarehouseOrderShipmentNormalizer())->normalize($shipments) : null;

                    $warehouseOrderItems[$warehouseName]['products'][$counter]['drop_down'] =
                        count($orderItem->getOrderItemDropDownOptions()) > 0 ? $this->getDropDowns($orderItem) : [];

                    $warehouseOrderItems[$warehouseName]['products'][$counter]['shipment'] = isset($value['shipment']) ?
                        (new WarehouseOrderShipmentNormalizer())->normalize($shipments) : null;

                    $counter++;
                }
            }

            // get usb coding products which not sent
            $warehouseOrderItems[$warehouseName]['order_item_usb_coding_free'] =
                isset($warehouse['order_item_usb_coding']) ? $this->container->
                get('app.serializer_normalizer.warehouse_order_item_usb_coding_normalizer')
                    ->normalize($warehouse['order_item_usb_coding'], null, ['check_is_usb_coding_sent' => false]) : [];

            // get usb coding products which already sent
            $warehouseOrderItems[$warehouseName]['order_item_usb_coding_sent'] =
                isset($warehouse['order_item_usb_coding']) ? $this->container->
                get('app.serializer_normalizer.warehouse_order_item_usb_coding_normalizer')
                    ->normalize($warehouse['order_item_usb_coding'], null, ['check_is_usb_coding_sent' => true]) : [];

            if (isset($warehouse['product_usb_coding'])) {
                $counter = 0;
                /** @var OrderItem $orderItem */
                foreach ($warehouse['product_usb_coding'] as $key => $orderItem) {
                    $warehouseOrderItems[$warehouseName]['product_usb_coding'][$counter] =
                        $this->container->get("app.serializer_normalizer.order_item_normalizer")
                            ->normalize($orderItem, null, ['usb_coding' => true]);
                    $counter++;
                }
            }
        }

        return $warehouseOrderItems;
    }

    /**
     * Get selected dropDown
     *
     * @param OrderItem $item
     * @return array
     */
    private function getDropDowns(OrderItem $item)
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
        }

        return $dropDownOptions;
    }

    private function warehouse($warehouse)
    {
        return [
            'id' => $warehouse->getId(),
            'name' => $warehouse->getName()
        ];
    }

    private function getShippingMethods(Warehouse $warehouse)
    {
        $shippingMethods = $warehouse->getShippingMethod();

        $tableItem = [];
        /** @var ShippingMethod $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            $tableItem[] = [
                'id' => $shippingMethod->getId(),
                'name' => $shippingMethod->getName(),
                'code' => $shippingMethod->getCode()
            ];
        }

        return $tableItem;
    }

    /**
     * @param $items
     * @return array
     */
    public function getShipments($items)
    {
        $trackingArray = [];
        $trackingNumbersArray = [];

        foreach ($items as $item) {
            $shipments = is_array($item['shipment']) ? $item['shipment'] : [];

            /** @var Shipment $shipment */
            foreach ($shipments as $shipment) {
                $trackingNumber = null;
                $trackingNumber = !empty($shipment) ? $shipment->getTracking() : null;
                if (!empty($trackingNumber && !in_array($trackingNumber, $trackingNumbersArray))) {
                    $trackingNumbersArray[] = $trackingNumber;
                    $trackingArray[] = [
                        'id' => $shipment->getId(),
                        'tracking_number' => !empty($shipment->getTracking()) ? $shipment->getTracking() : ""
                    ];
                }
            }
        }

        return $trackingArray;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Warehouse;
    }
}
