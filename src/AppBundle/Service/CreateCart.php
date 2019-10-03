<?php

namespace AppBundle\Service;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\SavePrice;
use AppBundle\Repository\DropDownOptionRepository;
use AppBundle\Repository\OrderItemRepository;
use AppBundle\Repository\OrderRepository;
use AppBundle\Repository\ProductVariantRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class CreateCart extends Controller
{
    const LOCALE_CODE = 'en_us';
    const CURRENCY_CODE = 'USD';

    /**
     * @var ProductVariantRepository
     */
    private $productVariantRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var DropDownOptionRepository
     */
    private $dropDownOptionRepository;

    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;


    /**
     * CartController constructor.
     *
     * @param ProductVariantRepository $productVariantRepository
     * @param ObjectManager $objectManager
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     * @param OrderRepository $orderRepository
     * @param DropDownOptionRepository $dropDownOptionRepository
     * @param OrderItemRepository $orderItemRepository
     */
    public function __construct(
        ProductVariantRepository $productVariantRepository,
        ObjectManager $objectManager,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderRepository $orderRepository,
        DropDownOptionRepository $dropDownOptionRepository,
        OrderItemRepository $orderItemRepository
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->objectManager = $objectManager;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderRepository = $orderRepository;
        $this->dropDownOptionRepository = $dropDownOptionRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Create Order = Cart
     *
     * @param array $products
     * @param Channel $channel
     * @param Channel $mainChannel
     * @return Order
     */
    public function create(array $products, Channel $channel, Channel $mainChannel)
    {
        $cart = new Order();

        $cart->setLocaleCode(self::LOCALE_CODE);
        $cart->setCurrencyCode(self::CURRENCY_CODE);
        $cart->setState('cart');
        $cart->setChannel($mainChannel);

        $cart = $this->createItems($cart, $products, $channel);

        return $cart;
    }

    /**
     * Create order items
     *
     * @param Order $order
     * @param array $products
     * @param Channel $channel
     * @return Order
     */
    public function createItems(Order $order, array $products, Channel $channel)
    {
        $variants = $this->getProductVariants(array_keys($products));

        /** @var ProductVariant $variant */
        foreach ($variants as $variant) {
            $item = new OrderItem();

            $quantity = $products[$variant->getId()];

            $item->setVariant($variant);
            $item->setUnitPrice($variant->getChannelPricingForChannel($channel)->getPrice());

            $this->orderItemQuantityModifier->modify($item, $quantity);
            $this->addWarehouseToOrderItemUnits($item);
            $order->addItem($item);
        }

        return $order;
    }

    /** Create or update order item
     *
     * @param ProductVariant $productVariant
     * @param Channel $channel
     * @param Order $order
     * @param null $type
     * @param bool $compatible
     * @param $savePrice
     * @return OrderItem
     */
    public function createOrderItem(
        ProductVariant $productVariant,
        Channel $channel,
        Order $order,
        $type = null,
        $compatible = false,
        $savePrice = false
    )
    {
        $price = $productVariant->getChannelPricingForChannel($channel)->getPrice();

        if ($type == 'warranty' && $price == 1) {
            $price = 0;
        }

        if ($type == 'includedAddon') {
            $price = 0;
        }


//        /** @var OrderItem $orderItem */
//        if ($orderItem = $this->orderItemRepository->getOrderItemByVariantByOrder($productVariant, $order)) {
//
//            $this->orderItemQuantityModifier->modify($orderItem, $orderItem->getQuantity() + 1);
//            return $orderItem;
//        }

        $item = new OrderItem();
        $item->setVariant($productVariant);
        $item->setType($type);
        $item->setCompatible($compatible);
        $item->setWarehouse($productVariant->getDefaultWarehouse());
        $this->orderItemQuantityModifier->modify($item, 1);
        $this->addWarehouseToOrderItemUnits($item);

        if ($savePrice) {
            /** @var Product $product */
            $product = $productVariant->getProduct();
            $item->setSavePrice($product->getSavePrice());
        }
        $item->setPrice($price);
        $order->addItem($item);

        return $item;
    }

    /**
     * @param $addons
     * @param OrderItem $orderItem
     * @param Channel $channel
     * @param null $type
     * @param string $compatibility
     * @return OrderItem|void
     */
    public function addAddons($addons, OrderItem $orderItem, Channel $channel, $type = null, $compatibility = 'Not verified')
    {

        if (is_array($addons)) {
            /** @var ProductVariant $variant */
            foreach ($addons as $addon) {
                $orderItem->addAddon($this->createOrderItem($addon, $channel, $orderItem->getOrder(), $type, $compatibility));
            }
            return $orderItem;
        }

        return $orderItem->addAddon($this->createOrderItem($addons, $channel, $orderItem->getOrder(), $type, $compatibility));
    }


    /**
     * Get product variant
     *
     * @param $data
     * @return mixed|null|object
     */
    public function getProductVariants($data)
    {
        if (is_array($data)) {
            return $this->productVariantRepository->getProductVariantsByIds($data);
        }
        return $this->productVariantRepository->findOneById($data);
    }

    /**
     * Find or create object
     *
     * @param int $id
     * @param Channel $mainChannel
     * @return Order|null|object
     */
    public function findOrCreateOrder($id, Channel $mainChannel)
    {

        if ($order = $this->orderRepository->find($id)) {
            return $order;
        }

        $order = new Order();

        $order->setLocaleCode(self::LOCALE_CODE);
        $order->setCurrencyCode(self::CURRENCY_CODE);
        $order->setState('cart');
        $order->setChannel($mainChannel);

        $this->orderRepository->add($order);

        $session = new Session();
        $session->set('_sylius.cart.' . $order->getChannel()->getCode(), $order->getId());

        return $order;
    }

    /**
     * Get drop down option by ids
     *
     * @param $ids
     * @return array|mixed
     */
    public function getDropDownOptions($ids)
    {
        if (!$ids) {
            return [];
        }
        return $this->dropDownOptionRepository->getDropDownOptionByIds($ids);
    }

    /**
     * Add drop down option to order item
     *
     * @param OrderItem $orderItem
     * @param array $dropDowns
     * @return OrderItem
     */
    public function addDropDownOptions(OrderItem $orderItem, array $dropDowns)
    {
        if (!$dropDowns) {
            return $orderItem;
        }

        /** @var DropDownOption $item */
        foreach ($dropDowns as $item) {
            if (!$item->getIsNone()) {
                $type = $item->getDropDown()->getType();
                $orderItemDropDownOption = new OrderItemDropDownOption();
                $orderItemDropDownOption->setType($type);
                $orderItemDropDownOption->setDropDownOption($item);
                $orderItem->addOrderItemDropDownOption($orderItemDropDownOption);
                $price = $orderItem->getPrice() + $item->getPrice();
                $orderItem->setPrice($price);
                if ($type == DropDown::CODDING_PRODUCT || DropDown::PHYSICAL_PRODUCT_WITH_CODDING) {
                    $orderItemDropDownOption->setState(OrderInterface::STATUS_NOT_CODED);
                }
            }
        }

        return $orderItem;
    }

    /**
     * Add save price
     *
     * @param OrderItem $orderItem
     * @param $savePrice
     * @return OrderItem
     */
    public function addSavePrice(OrderItem $orderItem, $savePrice)
    {
        /** @var SavePrice $productSavePrice */
        if ($savePrice && $productSavePrice = $orderItem->getProduct()->getSavePrice()) {
            $price = $orderItem->getPrice() - $productSavePrice->getPrice();
            $orderItem->setPrice($price);
        }

        return $orderItem;
    }

    public function addWarehouseToOrderItemUnits(OrderItem &$orderItem)
    {
        $orderItemUnits = $orderItem->getUnits();
        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            $orderItemUnit->setWarehouse($orderItem->getWarehouse());
        }
    }

    /**
     * @param Order $order
     * @param $sessionCartId
     */
    public function mergeSessionCartToCustomer(Order $order, $sessionCartId)
    {
        /** @var Order $sessionCart */
        if ($sessionCart = $this->orderRepository->find($sessionCartId)) {
            if ($sessionCartId != $order->getId()) {
                /** @var OrderItem $item */
                foreach ($sessionCart->getItems() as $item) {
                    $item->setOrder($order);
                    $this->orderItemRepository->add($item);
                }
                $this->orderRepository->remove($sessionCart);
            }
        }
    }
}
