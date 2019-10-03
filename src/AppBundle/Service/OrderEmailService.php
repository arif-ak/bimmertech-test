<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Repository\OrderRepository;

/**
 * Class OrderEmailService
 * @package AppBundle\Service
 */
class OrderEmailService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderEmailService constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getData($orderId)
    {
        /** @var Order $order */
        if ($order = $this->orderRepository->find($orderId)) {
            return [
                'url' => '/order/' . $order->getTokenValue() . '/pay',
                'order' => [
                    'id'=> $order->getId(),
                    'number'=>$order->getNumber(),
                    'customer' =>$order->getCustomer()->getFullName()
                    ]
            ];
        }
    }
}