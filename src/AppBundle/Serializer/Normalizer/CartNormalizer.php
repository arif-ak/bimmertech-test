<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\Product;
use AppBundle\VinCheck\CompatibilityProducts;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class OrderNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class CartNormalizer
{
    /**
     * @var CompatibilityProducts
     */
    private $compatibilityService;

    /**
     * @var OrderItemIncludedAddonsNormalizer
     */
    private $orderItemIncludedAddonsNormalizer;

    /**
     * OrderNormalizer constructor.
     *
     * @param CompatibilityProducts $compatibilityService
     * @param OrderItemIncludedAddonsNormalizer $orderItemIncludedAddonsNormalizer
     */
    public function __construct($compatibilityService, $orderItemIncludedAddonsNormalizer)
    {
        $this->compatibilityService = $compatibilityService;
        $this->orderItemIncludedAddonsNormalizer = $orderItemIncludedAddonsNormalizer;
    }

    /**
     * @param $cart
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($cart, $format = null, array $context = [])
    {
        $channel = $context['channel'];
        $session = $context['session'];

        /** @var Order $cart */
        $items = $cart->getItems()->filter(function ($item) {
            /** @var OrderItem $item */
            if (!$item->getParent()) {
                return $item;
            }
        });

        $orderItems = [];
        foreach ($items as $item) {

            $orderItem = $this->prepareData($item, $session);

            $array = [
                'dropDown' => $this->getDropDowns($item),
                'addons' => $this->getAddons($item, $session),
                'includedAddons' => $this->getIncludedAddons($item, $context),
            ];

            $orderItems[] = array_merge($orderItem, $array, $this->getWarranties($item, $channel));
        }

        return [
            'back_to_store' => $items->count() > 0 ? '/products-' . $items->last()->getProduct()->getSlug() : '/',
            'cart_id' => $cart->getId(),
            'cart_total' => $cart->getTotal(),
            'cart_sub_total' => $cart->getTotal(),
            'cart_items' => $orderItems,
            'compatibility' => $cart->getCompatibility()
        ];
    }


    /**
     * Prepare order item data
     *
     * @param OrderItem $item
     * @param null $session
     * @return array
     */
    private function prepareData(OrderItem $item, $session = null)
    {

        $image = '/images/no-image.png';
        $savePrice = null;
        if ($item->getProduct()->getImages()->count()) {
            $imageName = '/media/cache/product_50_25/' . $item->getProduct()->getImages()->first()->getPath();
            $imageExist = $_SERVER['DOCUMENT_ROOT'] . $imageName;

            if (file_exists($imageExist)) {
                $image = $imageName;
            } else {
                $image = '/media/cache/resolve/product_50_25/' . $item->getProduct()->getImages()->first()->getPath();
            }
        }
        if ($item->getSavePrice()) {
            $savePrice = [
                'name' => $item->getSavePrice()->getTitle(),
                'price' => $item->getSavePrice()->getPrice()
            ];
        }

        return [
            'id' => $item->getId(),
            'quantity' => $item->getQuantity(),
            'unitPrice' => $item->getUnitPrice(),
            'total' => $item->getTotal(),
            'name' => $item->getVariantName() ?: $item->getProductName(),
            'productVariantId' => $item->getVariant()->getId(),
            'compatibility' => $item->getCompatible(),
            'image' => $image,
            'savePrice' => $savePrice
        ];
    }

    /**
     * Check compatibility
     *
     * @param $product
     * @param $session
     * @return mixed|string
     */
    private function checkCompatibility($product, $session)
    {
        /** @var Session $session */
        if ($vinData = $session->get('vincheck')) {
            return $this->compatibilityService->checkCompatibilityProduct($product, $vinData);
        }
        return null;
    }

    /**
     * Get included addons
     *
     * @param OrderItem $item
     * @param array $context
     * @return array|bool|float|int|string
     */
    private function getIncludedAddons(OrderItem $item, $context = [])
    {
        return $this->orderItemIncludedAddonsNormalizer->normalize($item, null, $context);
    }

    /**
     * Get order item warranties
     *
     * @param OrderItem $item
     * @param $channel
     * @return array|bool|float|int|string
     */
    private function getWarranties(OrderItem $item, $channel)
    {
        $warranties['warranty'] = [];
        $warranties['selectedWarranty'] = null;

        /** @var OrderItem $item */
        if ($item->getAddons()) {
            /** @var OrderItem $addon */
            foreach ($item->getAddons() as $addon) {
                if ($addon->getType() == 'warranty') {
                    /** @var Product $product */
                    $product = $addon->getParent()->getProduct();
                    $warranties['warranty'] = (new OrderItemWarrantiesNormalizer())
                        ->normalize($product, null, [
                            'channel' => $channel,
                        ]);
                    $warranties['selectedWarranty'] = $addon->getProduct()->getVariants()->first()->getId();
                    $warranties['warrantyQuantity'] = $addon->getQuantity();
                }
            }
        }

        return $warranties;
    }

    /**
     * Get selected dropDown
     *
     * @param OrderItem $item
     * @return array
     */
    private function getDropDowns(OrderItem $item)
    {
        $dropDownOptions = [];
        if ($item->getOrderItemDropDownOptions()) {
            /** @var OrderItemDropDownOption $orderItemDropDownOption */
            foreach ($item->getOrderItemDropDownOptions() as $orderItemDropDownOption) {
                $downOption = $orderItemDropDownOption->getDropDownOption();
                /** @var DropDownOption $downOption */
                if ($downOption->getPrice()) {
                    $dropDownOptions[] = [
                        'name' => $downOption->getDropDown()->getName(),
                        'selected' => $downOption->getName()
                    ];
                }
            }
        }

        return $dropDownOptions;
    }

    /**
     * Get order item addons
     *
     * @param OrderItem $item
     * @param null $session
     * @return array
     */
    private function getAddons(OrderItem $item, $session = null)
    {

        $addons = [];
        /** @var OrderItem $item */
        if ($item->getAddons()) {

            /** @var OrderItem $addon */

            foreach ($item->getAddons() as $addon) {
                if ($addon->getType() == 'addon') {
                    $data = $this->prepareData($addon, $session);
                    $data['compatibility'] = $this->checkCompatibilityAddon($addon, $session);
                    $addons[] = $data;
                }
            }
        }
        return $addons;
    }

    /**
     * Check addon compatibility
     *
     * @param OrderItem $orderItem
     * @param null $session
     * @return string
     */
    private function checkCompatibilityAddon(OrderItem $orderItem, $session = null)
    {
        $productId = $orderItem->getProduct()->getId();
        $compatibility = 'No';

        if ($vinData = $session->get('vincheck')) {
            if (isset($vinData['products'])) {
                foreach ($vinData['products'] as $product) {
                    foreach ($product['addons'] as $addon) {
                        if ($addon['productId'] == $productId) {
                            $compatibility = 'Yes';
                        }
                    }
                }
                return $compatibility;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Order;
    }
}
