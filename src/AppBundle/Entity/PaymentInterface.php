<?php

namespace AppBundle\Entity;

use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;

interface PaymentInterface extends BasePaymentInterface
{
    public const STATE_PARTIALLY_REFUNDED = "partially refunded";
    public const STATE_PAID = "paid";
    public const STATE_AWAITING_PAYMENT = "awaiting_payment";
}
