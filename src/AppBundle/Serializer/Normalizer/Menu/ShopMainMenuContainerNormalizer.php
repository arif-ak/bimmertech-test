<?php

namespace AppBundle\Serializer\Normalizer\Menu;

use AppBundle\Entity\Taxon;

/**
 * Class OrderNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class ShopMainMenuContainerNormalizer
{

    /**
     *
     * @param Taxon $taxon
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($conteiners, $format = null, array $context = [])
    {
        $array = [];
        $channelPricing = null;

        /** @var Taxon $item */
        foreach ($conteiners as $item) {
            $file = $item->getImages()->count() ? $item->getImages()->first()->getPath() : null;
            $image = '/media/cache/resolve/product_70_52/' . $file;

            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'link' => '/category-' . $item->getSlug(),
                'image' => $image,
                'price' => $item->getPrice(),
                'container' => true,
                'description' => $item->getShortDescription(),
                'teaser'=>$item->getTeaser(),
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
