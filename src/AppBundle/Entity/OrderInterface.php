<?php

namespace AppBundle\Entity;

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

/**
 * Interface OrderInterface
 * @package AppBundle\Entity
 */
interface OrderInterface extends BaseOrderInterface
{
    const STATUS_NEW = 'new';
    const STATUS_COMPLETE = 'completed';
    const STATUS_NOT_REQUIRED = 'not required';
    const STATUS_PARTIALLY_ADDED = 'partially added';
    const STATUS_NOT_CODED = 'not coded';
    const STATUS_PLACED = 'placed';
    const STATUS_RETURNED = 'returned';
    const STATUS_PARTIALLY_RETURNED = 'partially returned';
    const STATUS_PARTIALLY_FULFILLED = 'partially fulfilled';

    const ORDER_CODDING_STATE = 'codding';
    const ORDER_SHIPMENT_STATE = 'shipment';
    const ORDER_PAYMENT_STATE = 'payment';
    const ORDER_SUPPORT_STATE = 'support';
    const ORDER_GENERAL_STATE = 'order';
}
