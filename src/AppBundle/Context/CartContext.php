<?php

namespace AppBundle\Context;

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;

final class CartContext implements CartContextInterface
{

    /**
     * {@inheritdoc}
     */
    public function getCart(): OrderInterface
    {
        throw new CartNotFoundException('Sylius was not able to find the cart in session');
    }
}
