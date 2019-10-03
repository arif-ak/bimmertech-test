<?php

namespace AppBundle\Serializer\Normalizer\User;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CodingNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class CodingNormalizer implements NormalizerInterface
{
    private $codingSession;

    public function __construct($codingSession)
    {
        $this->codingSession = $codingSession;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $items = [];
        $orderId = null;

        /** @var OrderItem $item */
        foreach ($object as $item) {

            if ($orderId != $item->getOrder()->getId()) {
                $items[$item->getOrder()->getId()] = [
                    'id' => $item->getOrder()->getId(),
                    'number' => $item->getOrder()->getNumber(),
                    'date' => $item->getOrder()->getCheckoutCompletedAt()->format('d.m.Y'),
                    'orderItems' => [],
                ];
                $orderId = $item->getOrder()->getId();
            }

            if ($item->getType() == 'item') {

                $items[$item->getOrder()->getId()] ['orderItems'][] = [
                    'order' => $item->getOrder()->getId(),
                    'id' => $item->getId(),
                    'name' => $item->getProductName(),
                    'instruction' => $this->getInstruction($item),
                    'coding' => $this->getCodingStatus($item->getCodingStatus()),
                    'coding_session' => $this->getCodingStatus($item->getCodingStatus()) == OrderItemInterface::NA ? OrderItemInterface::NA : $this->codingSession,
                    'dropDowns' => $this->getDropDowns($item),
                    'addons' => $this->getAddons($item, 'addon'),
                    'includedAddons' => $this->getAddons($item, 'includedAddon')
                ];
            }
        }

        return array_values($items);
    }

    /**
     * @param OrderItem $orderItem
     * @return array
     */
    private function getDropDowns(OrderItem $orderItem)
    {
        $dropdowns = [];
        /** @var Collection $collections */
        $collections = $orderItem->getOrderItemDropDownOptions();
        foreach ($collections as $item) {
            /** @var OrderItemDropDownOption $item */
            if ($item->getType() == 'coding' || $item->getType() == 'physical_coding') {
                $dropdowns[] = [
                    'name' => $item->getDropDownOption()->getDropDown()->getName(),
                    'selected' => $item->getDropDownOption()->getName(),
                    'coding' => $this->getCodingStatus($item->getState())
                ];
            }
        }

        return $dropdowns;
    }

    /**
     * @param OrderItem $orderItem
     * @return array
     */
    private function getInstruction(OrderItem $orderItem)
    {

        switch ($orderItem->getSupportStatus()) {
            case OrderItemInterface::NOT_ADDED;
                return [
                    'status' => OrderItemInterface::NOT_ADDED,
                    'statusMessage' => OrderItemInterface::IN_PROCESS,
                    'url' => null,
                ];
            case OrderItemInterface::COMPLETE;

                if ($orderItem->getInstruction() == OrderItemInterface::IN_VIA_EMAIL) {
                    return [
                        'status' => OrderItemInterface::COMPLETE,
                        'statusMessage' => OrderItemInterface::IN_VIA_EMAIL,
                        'url' => null,
                    ];
                }
                return [
                    'status' => OrderItemInterface::COMPLETE,
                    'statusMessage' => OrderItemInterface::DOWNLOAD,
                    'url' => $orderItem->getInstruction(),
                ];
            case OrderItemInterface::NOT_REQUIRED;
                return [
                    'status' => OrderItemInterface::NOT_REQUIRED,
                    'statusMessage' => OrderItemInterface::NA,
                    'url' => null,
                ];
        }
    }

    /**
     * Convert coding status;
     * @param $value
     * @return string
     */
    private function getCodingStatus($value)
    {
        switch ($value) {
            case OrderItemInterface::NEW;
                return OrderItemInterface::IN_PROCESS;
            case OrderItemInterface::COMPLETE;
                return OrderItemInterface::COMPLETE;
            case OrderItemInterface::NOT_REQUIRED;
                return OrderItemInterface::NA;
            case OrderItemInterface::NOT_CODED;
                return OrderItemInterface::IN_PROCESS;
        }
    }

    /**
     * @param OrderItem $orderItem
     * @param $type
     * @return array
     */
    private function getAddons(OrderItem $orderItem, $type)
    {
        $addon = [];
        /** @var OrderItem $value */
        foreach ($orderItem->getAddons() as $value) {
            if ($value->getType() == $type) {
                $addon [] = [
                    'id' => $value->getId(),
                    'name' => $value->getProductName(),
                    'instruction' => $this->getInstruction($value),
                    'coding_session' => $this->getCodingStatus($value->getCodingStatus()) == OrderItemInterface::NA ? OrderItemInterface::NA : $this->codingSession,
                    'coding' => $this->getCodingStatus($value->getCodingStatus()),
                    'quantity' => $value->getQuantity()
                ];
            }
        }
        return $addon ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItem;
    }
}
