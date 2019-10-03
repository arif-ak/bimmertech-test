<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\PaymentMethod;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailCanceledPaymentPayPal
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Order $order
     */
    public function sendEmailCanceledPaymentPayPal(Order $order)
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $order->getLastPayment('awaiting_payment')->getMethod();
        if ($paymentMethod->getGatewayConfig()->getFactoryName() === 'paypal_express_checkout') {
        }
    }
}
