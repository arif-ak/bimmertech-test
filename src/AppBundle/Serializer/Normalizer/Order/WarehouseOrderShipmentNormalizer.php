<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\Shipment;
use AppBundle\Entity\Warehouse;

class WarehouseOrderShipmentNormalizer
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $warehouseOrderShipment = [];
        /** @var Shipment $shipment */
        foreach ($object as $key => $shipment) {
            $warehouseOrderShipment[] = [
                'id' => $shipment->getId(),
                'tracking_number' => !empty($shipment->getTracking()) ?  $shipment->getTracking() : ""
            ];
        }

        return $warehouseOrderShipment;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function productNormalize($object)
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'is_recommended' => $object->getRecomended(),
            'shortDescription' => $object->getShortDescription(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Warehouse;
    }
}
