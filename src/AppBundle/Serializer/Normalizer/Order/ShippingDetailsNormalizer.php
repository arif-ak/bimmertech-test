<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\Shipment;
use AppBundle\Entity\Warehouse;
use AppBundle\Serializer\Normalizer\MediaNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingDetailsNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * PayPalService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Shipment $shipment */
        $shipment = $object;
        $orderItemsUnits = $shipment->getUnits()->count() > 0 ? $shipment->getUnits() : [];

        $shippingExportObj = $context['shippingExportObj'];
        if ($shippingExportObj) {
            $filePath = $this->filePath($shippingExportObj);
        } else {
            $filePath = null;
        }

        /** @var Shipment $shipment */
        $shipping = [
            'id' => $shipment->getId(),
            'tracking_number' => !empty($shipment->getTracking()) ? $shipment->getTracking() : null,
            'courier' => !empty($shipment->getCourier()) ? $shipment->getCourier() : null,
            'dhl_weight' => !empty($shipment->getDhlWeight()) ? $shipment->getDhlWeight() : null,
            'ship_method' => $shipment->getMethod() ? $this->getShippingMethods($shipment->getMethod()) : [],
            'insured_amount' => $shipment->getDhlInsuredAmount(),
            'number_of_pieces' => $shipment->getDhlNumberOfPieces(),
            'length' => $shipment->getLength() ? $shipment->getLength() : null,
            'height' => $shipment->getHeight() ? $shipment->getHeight() : null,
            'width' => $shipment->getWidth() ? $shipment->getWidth() : null,
            'images' => (new MediaNormalizer())->normalize($shipment),
            'label' => $filePath,
            'order_item_units' => $this->container->
            get('app.serializer_normalizer.warehouse_order_item_unit_normalizer')->normalize($orderItemsUnits)
        ];

        return $shipping;
    }

    private function getShippingMethods($shippingMethod)
    {
        return [
            'id' => $shippingMethod->getId(),
            'name' => $shippingMethod->getName(),
            'code' => $shippingMethod->getCode()
        ];
    }

    public function filePath($shippingExportObj)
    {
        $filePath = null;
        $explodeLabelPath = $shippingExportObj ? explode("/", $shippingExportObj->getLabelPath()) : '';
        if (count($explodeLabelPath) > 1) {
            $doc = end($explodeLabelPath);
            $filePath = '/shipping_labels/' . $doc;
        }

        return $filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Warehouse;
    }
}
