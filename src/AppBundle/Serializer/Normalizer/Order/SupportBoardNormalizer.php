<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\PhotoReport;
use AppBundle\Entity\Shipment;
use AppBundle\Serializer\Normalizer\OrderItem\OrderItemNormalizer;

class SupportBoardNormalizer
{
    /**
     * @var OrderItemNormalizer
     */
    private $orderItemNormalizer;

    /**
     * SupportBoardNormalizer constructor.
     * @param OrderItemNormalizer $orderItemNormalizer
     */
    public function __construct(
        OrderItemNormalizer $orderItemNormalizer
    )
    {
        $this->orderItemNormalizer = $orderItemNormalizer;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $items = $object;

        $listOfOrderItemInstruction = [];
        /** @var OrderItem $item */
        foreach ($items as $key => $item) {
            if ($item->getType() != OrderItemInterface::TYPE_WARRANTY) {
                $listOfOrderItemInstruction[$key] = $this->orderItemNormalizer
                    ->normalize($item, null, ['support_board' => true]);
                $listOfOrderItemInstruction[$key]['shipping_images'] = $this->getSippingImages($item);
            }
        }

        return $listOfOrderItemInstruction;
    }

    private function getSippingImages(?OrderItem $orderItem)
    {
        $orderItemUnits = $orderItem->getUnits();
        $images = [];
        $shipments = [];
        $filteredShipments = [];
        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            $shipment = $orderItemUnit->getShipment();
            if ($shipment && !in_array($shipment->getId(), $shipments)) {
                $shipments[] = $shipment->getId();
                $filteredShipments[] = $shipment;
            }
        }

        /** @var Shipment $shipment */
        foreach ($filteredShipments as $shipment) {
            $shipmentImages = $shipment->getImages();
            /** @var PhotoReport $image */
            foreach ($shipmentImages as $image) {
                $images[] = [
                    'id' => $image->getId(),
                    'name' =>'/media/image/'. $image->getPath(),
                ];
            }
        }

        return $images;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItem;
    }
}
