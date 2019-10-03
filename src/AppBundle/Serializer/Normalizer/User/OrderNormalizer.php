<?php

namespace AppBundle\Serializer\Normalizer\User;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentInterface;
use AppBundle\Entity\ShippingMethodInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class OrderNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class OrderNormalizer
{
    const AFTER_SHIP_NAME = [
        'dhl', "usps", "ems"
    ];

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  Order $order
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($order, $format = null, array $context = [])
    {
        return [
            'id' => $order->getId(),
            'number' => $order->getNumber(),
            'date' => $order->getCreatedAt()->format('d.m.Y'),
            'orderStatus' => $this->format($order->getState()),
            'paymentStatus' => $this->format($order->getPaymentState()),
            'paymentUrl' => $order->getPaymentState() == 'awaiting_payment' ? $context['host'] . '/order/' . $order->getTokenValue() . '/pay' : null,
            'shippingStatus' => $this->format($order->getShippingState()),
            'totalAmount' => $order->getTotal(),
            'orderItems' => $this->getOrderItems($order),
        ];
    }

    /**
     * @param Order $order
     * @return array
     * @throws \Exception
     */
    private function getOrderItems(Order $order)
    {
        $array = [];
        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            if ($item->getType() == 'item') {

                /** @var OrderItem $item */
                $orderItemReturned = $this->container->get('app.service.order_item_board_type_service')->
                isAllOrderItemReturned($item);
                /** @var OrderItemUnit $unit */
                $unit = $item->getUnits()->first();
                $savePrice = null;
                if ($item->getSavePrice()) {
                    $savePrice = [
                        'name' => $item->getSavePrice()->getTitle(),
                        'price' => $item->getSavePrice()->getPrice()
                    ];
                }

                $array[] = [
                    'id' => $item->getId(),
                    'name' => $item->getProductName(),
                    'price' => $item->getTotal(),
                    'addons' => $this->getAddons($item),
                    'includedAddons' => $this->getAddons($item, 'includedAddon'),
                    'dropDowns' => $this->getDropDowns($item),
                    'trackingNumber' => $this->trackingNumbers($item),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                    'savePrice' => $savePrice,
                    'warranty' => $this->getWarranty($item),
                    'is_returned' => $orderItemReturned ? true : false
                ];
            }
        }

        return $array;
    }

    /**
     * @param OrderItem $orderItem
     * @return array|bool
     */
    private function getWarranty(OrderItem $orderItem)
    {
        if ($orderItem->getWarranty()) {
            return [
                'id' => $orderItem->getWarranty()->getId(),
                'name' => $orderItem->getWarranty()->getProduct()->getName(),
                'price' => $orderItem->getWarranty()->getTotal(),
                'quantity' => $orderItem->getWarranty()->getQuantity(),
            ];
        }
        return false;
    }

    /**
     * @param OrderItem $orderItem
     * @return array
     */
    private function getDropDowns(OrderItem $orderItem)
    {
        $array = [];
        /** @var OrderItemDropDownOption $item */
        foreach ($orderItem->getOrderItemDropDownOptions() as $item) {
            if ($item->getDropDownOption()->getName() != 'None') {
                $array[] = [
                    'name' => $item->getDropDownOption()->getDropDown()->getName(),
                    'selected' => $item->getDropDownOption()->getName()
                ];
            }
        }

        return $array;
    }

    /**
     * @param OrderItem $orderItem
     * @param string $type
     * @return array
     * @throws \Exception
     */
    private function getAddons(OrderItem $orderItem, $type = 'addon')
    {
        $array = [];
        /** @var OrderItem $item */
        foreach ($orderItem->getAddons() as $item) {
            if ($item->getType() == $type) {
                /** @var OrderItem $item */
                $orderItemReturned = $this->container->get('app.service.order_item_board_type_service')->
                isAllOrderItemReturned($item);
                $array[] = [
                    'id' => $item->getId(),
                    'name' => $item->getProductName(),
                    'price' => $item->getTotal(),
                    'trackingNumber' => $this->trackingNumbers($item),
                    'quantity' => $item->getQuantity(),
                    'is_returned' => $orderItemReturned ? true : false
                ];
            }
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Order;
    }

    /**
     * @param $string
     * @return string
     */
    private function format($string)
    {
        if ($string == 'new') {
            return 'Placed';
        }
        return ucwords(str_replace('_', " ", $string));
    }

    /**
     * @param $item
     * @return array
     * @throws \Exception
     */
    public function trackingNumbers($item)
    {
        $trackingArray = [];
        $trackingNumbersArray = [];
        /** @var OrderItemUnit $unit */
        $units = $item->getUnits();

        foreach ($units as $unit) {
            $trackingNumber = null;
            if ($unit->getShipment()) {
                $trackingNumber = !empty($unit->getShipment()->getTracking()) ?
                    $unit->getShipment()->getTracking() : null;
                if (!empty($trackingNumber && !in_array($trackingNumber, $trackingNumbersArray))) {
                    $trackingNumbersArray[] = $trackingNumber;

                    $url = $this->getUrlForShipment($unit->getShipment());

                    $trackingArray[] = [
                        'number' => $trackingNumber,
                        'url' => $url
                    ];
                }
            }
        }

        return $trackingArray;
    }

    /**
     * @param Shipment $shipment
     * @return string
     * @throws \Exception
     */
    public function getUrlForShipment($shipment)
    {

        $trackingNumber = !empty($shipment->getTracking()) ?
            $shipment->getTracking() : null;
        $ulr = "";
        $code = $shipment->getMethod()->getCode();
        if ($code == ShipmentInterface::GENERAL_METHOD) {
            $code = !empty($shipment->getCourier()) ? $shipment->getCourier() : "other";
        }

        $lowercaseCode = strtolower($code);
        
        if ($shipment->getTracking() && !empty($lowercaseCode)) {
            switch ($lowercaseCode) {
                case ShippingMethodInterface::DHL_AE:
                    $couriers = ShippingMethodInterface::AFTER_SHIP_DHL;
                    return "http://" . $_SERVER['HTTP_HOST'] . $this->container->get('router')->
                        generate('app_tracking_delivery_controller_account', [
                            'trackingNumber' => $trackingNumber, "couriers" => "$couriers"]);
//                return "http://www.dhl.com/en/express/tracking.html?AWB=$trackingNumber&brand=DHL";
                    break;
                case ShippingMethodInterface::DHL:
                    $couriers = ShippingMethodInterface::AFTER_SHIP_DHL;
                    return "http://" . $_SERVER['HTTP_HOST'] . $this->container->get('router')->
                        generate('app_tracking_delivery_controller_account', [
                            'trackingNumber' => $trackingNumber, "couriers" => "$couriers"]);
//                return "http://www.dhl.com/en/express/tracking.html?AWB=$trackingNumber&brand=DHL";
                    break;
                case ShippingMethodInterface::EMS:
                    $couriers = ShippingMethodInterface::EMS;
                    return "http://" . $_SERVER['HTTP_HOST'] . $this->container->get('router')->
                        generate('app_tracking_delivery_controller_account', [
                            'trackingNumber' => $trackingNumber, "couriers" => "$couriers"]);
                    break;
                case ShippingMethodInterface::USPS:
                    $couriers = ShippingMethodInterface::USPS;
                    return "http://" . $_SERVER['HTTP_HOST'] . $this->container->get('router')->
                        generate('app_tracking_delivery_controller_account', [
                            'trackingNumber' => $trackingNumber, "couriers" => "$couriers"]);
                    break;

                default:
                    return "http://" . $_SERVER['HTTP_HOST'] . $this->container->get('router')->
                        generate('app_tracking_delivery_controller_account', [
                            'trackingNumber' => $trackingNumber, "couriers" => $lowercaseCode]);
            }
        }

        return $ulr;
    }
}
