<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\ShopUser;
use AppBundle\Repository\ChannelRepository;
use AppBundle\Repository\OrderRepository;
use AppBundle\Serializer\Normalizer\CartNormalizer;
use AppBundle\Service\CreateCart;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository;
use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CartController
 * @package AppBundle\Controller\API
 */
class CartController extends Controller
{
    const LOCALE_CODE = 'en_us';
    const CURRENCY_CODE = 'USD';

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CreateCart;
     */
    private $createCart;

    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;

    /**
     * @var Order
     */
    private $orderRepository;

    /**
     * @var CartNormalizer
     */
    private $cartNormalizer;

    /**
     * CartController constructor.
     *
     * @param ChannelRepository $channelRepository
     * @param ObjectManager $objectManager
     * @param CreateCart $createCart
     * @param OrderItemRepository $orderItemRepository
     * @param OrderRepository $orderRepository
     * @param CartNormalizer $cartNormalizer
     */
    public function __construct(
        ChannelRepository $channelRepository,
        ObjectManager $objectManager,
        CreateCart $createCart,
        OrderItemRepository $orderItemRepository,
        OrderRepository $orderRepository,
        CartNormalizer $cartNormalizer
    )
    {
        $this->channelRepository = $channelRepository;
        $this->objectManager = $objectManager;
        $this->createCart = $createCart;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->cartNormalizer = $cartNormalizer;
    }

    /**
     * Show cart
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function showCart(Request $request)
    {
        /** @var Channel $mainChannel */
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        $cartId = $request->get('cartId') ?: $request->getSession()->get('_sylius.cart.' . $mainChannel->getCode());

        if (!$cartId) {
            return new JsonResponse($this->freeCart());
        }

        /** @var Order $cart */
        $cart = $this->orderRepository->find($cartId);
        if (!$cart) {
            return new JsonResponse($this->freeCart());
        }

        $data = $this->cartResponseData($cart, $mainChannel, $request->getSession());

        return new JsonResponse($data, 200);
    }

    /**
     * Update order item warranty
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateCartWarranty(Request $request)
    {
        /** @var OrderItem $orderItem */
        if (!$orderItem = $this->orderItemRepository->find($request->request->get('id') ?: 0)) {
            return new JsonResponse('Order item not found', 404);
        }
        /** @var ProductVariant $warranty */
        if (!$warranty = $this->createCart->getProductVariants($request->request->get('warrantyId') ?: 0)) {
            return new JsonResponse('Warranty not found', 404);
        }

        /** @var Channel $mainChannel */
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        /** @var Order $cart */
        $cart = $orderItem->getOrder();

        $price = $warranty->getChannelPricingForChannel($mainChannel)->getPrice();


        $quantityModifier = $this->get('sylius.order_item_quantity_modifier');
        $quantityModifier->modify($orderItem->getWarranty(), $orderItem->getQuantity());

        $orderItem->updateWarranty($warranty, $price == 1 ? 0 : $price);

        $this->orderRepository->add($cart);

        $data = $this->cartResponseData($cart, $mainChannel, $request->getSession());

        return new JsonResponse($data, 200);
    }

    /**
     * Update order item quantity
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateCartItem(Request $request)
    {
        /** @var OrderItem $orderItem */
        if (!$orderItem = $this->orderItemRepository->find($request->request->get('id') ?: 0)) {
            return new JsonResponse('Order item not found', 404);
        }

        /** @var Channel $mainChannel */
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        $quantityModifier = $this->get('sylius.order_item_quantity_modifier');
        $quantityModifier->modify($orderItem, $request->request->get('quantity') ?: 1);

        $this->updateAddons($orderItem);

        $this->createCart->addWarehouseToOrderItemUnits($orderItem);
        $data = $this->cartResponseData($orderItem->getOrder(), $mainChannel, $request->getSession());

        $this->objectManager->flush();

        return new JsonResponse($data, 200);
    }

    /**
     * Remove order item from cart
     *
     * @param Request $request
     * @param $orderId
     * @return JsonResponse
     */
    public function removeCartItem(Request $request, $orderId)
    {
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());

        if (!$cartId = $request->getSession()->get('_sylius.cart.' . $mainChannel->getCode())) {
            return new JsonResponse('Cart not found', 404);
        }
        /** @var OrderItem $orderItem */
        if (!$orderItem = $this->orderItemRepository->find($orderId)) {
            return new JsonResponse('Item not found', 404);
        }
        $cart = $orderItem->getOrder();
        if (count($orderItem->getAddons()) > 0) {
            foreach ($orderItem->getAddons() as $item) {
                $cart->removeItem($item);
            }
        }
        $cart->removeItem($orderItem);
        $this->objectManager->flush();
        if ($cart->getItems()->count() < 1) {
            $this->orderRepository->remove($cart);
        }
        return new JsonResponse('Item  removed', 204);
    }

    /**
     * Remove all order items from cart
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearCart(Request $request)
    {
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        $cartId = $request->getSession()->get('_sylius.cart.' . $mainChannel->getCode()) ?: 0;

        /** @var Order $cart */
        if (!$cart = $this->orderRepository->find($cartId)) {
            return new JsonResponse('Cart not found', 404);
        }

        $this->orderRepository->remove($cart);

        return new JsonResponse('The cart is empty', 200);
    }

    /**
     * Ajax add to cart product with addons
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addToCart(Request $request)
    {

        /** @var ProductVariant $product */
        if (!$productVariant = $this->createCart->getProductVariants($request->get('variantId'))) {
            return new JsonResponse('Product not found', 404);
        }
        $compatibility = $this->checkCompatibility($request, $productVariant->getProduct()->getId());
        /** @var Channel $mainChannel */
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        $orderId = $request->getSession()->get('_sylius.cart.' . $mainChannel->getCode(), 0);
        $savePrice = $request->get('savePrice');
        $cart = $this->createCart->findOrCreateOrder($orderId, $mainChannel);
        $orderItem = $this->createCart->createOrderItem($productVariant, $mainChannel, $cart, 'item', $compatibility, $savePrice);

        $warrantyId = $request->request->get('warranty', null);

        if ($warranty = $this->createCart->getProductVariants($warrantyId)) {
            $this->createCart->addAddons($warranty, $orderItem, $mainChannel, 'warranty', 'Compatible');
        }

        if ($ids = $request->request->get('addons', [])) {
            $mainChannel = $this->channelRepository->findOneByHostname(Product::ADDON_CAHANNEL);
            $addons = $this->createCart->getProductVariants($ids);
            $this->createCart->addAddons($addons, $orderItem, $mainChannel, 'addon', $compatibility);
        }

        if ($ids = $request->request->get('includedAddons', [])) {
            $includedAddons = $this->createCart->getProductVariants($ids);
            $this->createCart->addAddons($includedAddons, $orderItem, $mainChannel, 'includedAddon');
        }

        $dropDownOptions = $this->createCart->getDropDownOptions($request->request->get('dropDown'));

        $this->createCart->addDropDownOptions($orderItem, $dropDownOptions);

        $this->createCart->addSavePrice($orderItem, $request->request->get('savePrice'));

        /** @var ShopUser $user */
        if ($user = $this->getUser()) {
            $cart->setCustomer($user->getCustomer());
        }

        $this->orderRepository->add($cart);

        $data = $this->cartResponseData($cart, $mainChannel, $request->getSession());

        return new JsonResponse($data, 200);
    }

    /**
     * Prepare response
     *
     * @param Order $cart
     * @param null $channel
     * @return array
     */
    private function cartResponseData(Order $cart, $channel = null, $session = null)
    {
        $context = [
            'channel' => $channel,
            'session' => $session
        ];
        return $this->cartNormalizer->normalize($cart, null, $context);
    }

    /**
     * @return array
     */
    private function freeCart()
    {
        return [

            'back_to_store' => '/',
            'cart_id' => 0,
            'cart_total' => 0,
            'cart_sub_total' => 0,
            'cart_items' => []
        ];
    }

    /**
     * @param Request $request
     * @param $productId
     * @return string
     */
    private function checkCompatibility(Request $request, $productId)
    {

        if ($vincheck = $request->getSession()->get('vincheck')) {

            if (isset($vincheck['products'])) {
                $compatibility = array_filter($vincheck['products'], function ($array) use ($productId) {
                    return $array['productId'] == $productId ? true : false;
                });
            }

            return (bool)$compatibility ? 'Compatible' : 'Not compatible';
        }

        return 'Not verified';
    }

    private function updateAddons(OrderItem $orderItem)
    {
        $quantityModifier = $this->get('sylius.order_item_quantity_modifier');
        /** @var OrderItem $item */
        foreach ($orderItem->getAddons() as $item) {
            $quantityModifier->modify($item, $orderItem->getQuantity());
            $this->createCart->addWarehouseToOrderItemUnits($item);
        }

        $this->orderItemRepository->add($orderItem);
    }
}
