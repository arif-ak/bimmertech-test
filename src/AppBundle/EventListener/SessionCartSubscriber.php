<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Order;
use AppBundle\Service\CreateCart;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SessionCartSubscriber implements EventSubscriberInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * @var CreateCart
     */
    private $cartService;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param CartContextInterface $cartContext
     * @param CartStorageInterface $cartStorage
     * @param CreateCart $cartService
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(
        CartContextInterface $cartContext,
        CartStorageInterface $cartStorage,
        CreateCart $cartService,
        CustomerContextInterface $customerContext
    )
    {

        $this->customerContext = $customerContext;
        $this->cartContext = $cartContext;
        $this->cartStorage = $cartStorage;
        $this->cartService = $cartService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {


        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var Session $session */
        $session = $event->getRequest()->getSession();
        if ($session && !$session->isStarted()) {
            return;
        }

        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return;
        }


        $this->saveCartSession($cart, $session);


        if (null !== $cart && null !== $cart->getId() && null !== $cart->getChannel()) {
            $this->cartStorage->setForChannel($cart->getChannel(), $cart);
        }
    }


    /**
     * @param Order $cart
     * @param Session $session
     * @return Order
     */
    private function saveCartSession($cart, $session)
    {
        if (!$cart->getCustomer() ) {
            $session->set('_last_cart', $cart->getId());
        }

        if ($cart->getCustomer() && $session->get('_last_cart')) {
            $this->cartService->mergeSessionCartToCustomer($cart, $session->get('_last_cart'));
            $session->remove('_last_cart');
        }
    }
}
