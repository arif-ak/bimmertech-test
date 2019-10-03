<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\Order;
use AppBundle\Entity\PaymentInterface;

class OrderStatesNormalizer
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Order $order */
        $order = $object;

        return [
            'vin' => $order->getVin() ? $order->getVin() : "",
            'compatibility'=>$order->getCompatibility(),
            'general_status' => $order->getState(),
            'payment_status' => $order->getPaymentState() ==
                PaymentInterface::STATE_AWAITING_PAYMENT ? "awaiting payment" : $order->getPaymentState(),
            'shipment_status' => $order->getShippingState(),
            'support_status' => $order->getSupportStatus(),
            'coding_status' => $order->getCodingStatus(),
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
