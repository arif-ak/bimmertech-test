<?php

namespace AppBundle\Service;

use AppBundle\Entity\BuyersGuideProductOption;
use AppBundle\Entity\BuyersGuideProductRelated;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BuyersGuideService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ShippingAddressPayPal constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buyersGuideHelper(Product $product)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->container->get('sylius.context.channel')->getChannel();

        $productsOption = [
            'headerContent' => $product->getBuyersHeaderContent(),
            'footerContent' => $product->getBuyersFooterContent()
        ];

        /** @var Product $product */
        $currentProductOptions = $product->getBuyersOption()->count() > 0 ? $product->getBuyersOption() : null;
        $relatedProducts = $product->getBuyersRelated()->count() > 0 ? $product->getBuyersRelated() : null;

        /** @var Product $object */
        /** @var ProductVariant $variant */
        $variant = $product->getVariants()->first();
        $channelPricing = $variant->getChannelPricingForChannel($channel);

        $optionKeyArray = [];
        $currentProductOptionArray = [
            'name' => $product->getName(),
            'image' => $product->getBuyersImage()->count() > 0 ? $product->getBuyersImage()->first()->getPath() : null,
            'compatibility' => $product ?
                $product->getCompatibility() : null,
        ];

        foreach ($currentProductOptions as $optionKey) {
            $optionKeyArray[] = $optionKey->getOption()->getName();
            $currentProductOptionArray['options'][$optionKey->getOption()->getName()] = [
                'name' => $optionKey->getValue(),
                'image' => $optionKey->getPath()
            ];
            // Check is price
            if ($optionKey->getOption()->getName() == 'Price') {
                $currentProductOptionArray['options'][$optionKey->getOption()->getName()] = [
                    'name' => number_format(($channelPricing->getPrice() / 100), 2),
                    'image' => $optionKey->getPath()
                ];
            }
        }

        foreach ($relatedProducts as $relatedProduct) {
            /** @var Product $object */
            /** @var ProductVariant $variant */
            if (!$relatedProduct->getRelatedProduct()) {
                return;
            }
            $variant = $relatedProduct->getRelatedProduct()->getVariants()->first();
            $channelPricing = $variant->getChannelPricingForChannel($channel);


            /** @var BuyersGuideProductRelated $relatedProductOptionArray */
            $relatedProductOptionArray = [
                'name' => $relatedProduct->getRelatedProduct() ?
                    $relatedProduct->getRelatedProduct()->getName() : null,
                'image' => $relatedProduct->getRelatedProduct()->getBuyersImage()->count() ?
                    $relatedProduct->getRelatedProduct()->getBuyersImage()->first()->getPath() : null,
                'compatibility' => $relatedProduct->getRelatedProduct() ?
                    $relatedProduct->getRelatedProduct()->getCompatibility() : null,
            ];

            $relatedProductOptions = $relatedProduct->getRelatedProduct()->getBuyersOption();

            /**
             * @var BuyersGuideProductOption $relatedProductOption
             */
            foreach ($optionKeyArray as $optionKey) {
                $relatedProductOptionArray['options'][$optionKey] = [
                    'name' => null,
                    'image' => null
                ];

                foreach ($relatedProductOptions as $relatedKey => $relatedProductOption) {
                    if ($optionKey == $relatedProductOption->getOption()->getName()) {
                        $relatedProductOptionArray['options'][$relatedProductOption->getOption()->getName()] = [
                            'name' => $relatedProductOption->getValue() ? $relatedProductOption->getValue() : null,
                            'image' => $relatedProductOption->getPath() ? $relatedProductOption->getPath() : null
                        ];
                    }
                }

                // Check is price OPTION
                if ($optionKey == 'Price') {
                    $relatedProductOptionArray['options'][$optionKey] = [
                        'name' => number_format(($channelPricing->getPrice() / 100), 2),
                        'image' => null
                    ];
                }
            }

            $productsOption['products'][] = $relatedProductOptionArray;
        }

        $productsOption['products'][] = $currentProductOptionArray;

        return $productsOption;
    }
}
