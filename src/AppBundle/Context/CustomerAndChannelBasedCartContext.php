<?php

namespace AppBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class CustomerAndChannelBasedCartContext implements CartContextInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param CustomerContextInterface $customerContext
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->customerContext = $customerContext;
        $this->channelContext = $channelContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(): OrderInterface
    {


        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no current channel.');
        }

        $customer = $this->customerContext->getCustomer();
        if (null === $customer) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no logged in user.');
        }

        $cart = $this->orderRepository->findLatestCartByChannelAndCustomer($channel, $customer);

        if (null === $cart) {
            throw new CartNotFoundException('Sylius was not able to find the cart for currently logged in user.');
        }


        return $cart;
    }

    /**
     * @param Order $cart
     * @param Session $session
     * @return Order
     */
    private function saveCartSession($cart, $session)
    {
        if (!$cart->getCustomer() && $session->get('_last_cart') != $cart->getId()) {
            $session->set('_last_cart', $cart->getId());
        }

        if ($cart->getCustomer() && $session->get('_last_cart')) {
            $this->cartService->mergeSessionCartToCustomer($cart, $session->get('_last_cart'));
            $session->remove('_last_cart');
        }
    }
}
