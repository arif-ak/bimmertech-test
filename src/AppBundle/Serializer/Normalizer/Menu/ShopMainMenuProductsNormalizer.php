<?php

namespace AppBundle\Serializer\Normalizer\Menu;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Taxon;

/**
 * Class OrderNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ShopMainMenuProductsNormalizer
{

    /**
     * Normalize Taxon  products
     *
     * @param $products
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($products, $format = null, array $context = [])
    {
        $array = [];
        $channelPricing = null;
        /** @var Product $item */
        foreach ($products as $item) {
            if (isset($context['channel'])) {
                /** @var ProductVariant $variant */
                $variant = $item->getVariants()->first();
                $channelPricing = $variant->getChannelPricingForChannel($context['channel']);
            }
            $image = '/images/no-image.png';
            if ($item->getImages()->count()) {
                $imageName = '/media/cache/product_70_52/' . $item->getImages()->first()->getPath();
                $imageExist = __DIR__ . '/../../../../../' .'web'. $imageName;
                if (file_exists($imageExist)){
                    $image = $imageName;
                } else{
                    $image  = '/media/cache/resolve/product_70_52/' . $item->getImages()->first()->getPath();
                }
            }

            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'link' => '/products-' . $item->getSlug(),
                'image' => $image,
                'price' => $item->getVariants()->count() > 1 ? null : !$channelPricing ? null : $channelPricing->getPrice(),
                'container' => false,
                'description' => $item->getTaxonDescription(),
            ];
        }
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Taxon;
    }
}
