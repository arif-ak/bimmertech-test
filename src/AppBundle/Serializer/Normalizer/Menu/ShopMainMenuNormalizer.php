<?php

namespace AppBundle\Serializer\Normalizer\Menu;

use AppBundle\Entity\Taxon;

/**
 * Class OrderNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class ShopMainMenuNormalizer
{

    /**
     * @param $cart
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($menu, $format = null, array $context = [])
    {
        $array = [];

        /** @var Taxon $item */
        foreach ($menu as $item) {

            $file = $item->getImages()->count() ? $item->getImages()->first()->getPath() : null;
            $image = '/media/cache/resolve/product_70_52/' . $file;

            if (preg_match('/\.(SVG|svg)$/', $file)) {
                $image = '/media/image/' . $file;
            }

            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'link' => $this->replaсeUrl($item),
                'image' => $image
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

    /**
     * @param Taxon $taxon
     * @return string
     */
    private function replaсeUrl(Taxon $taxon)
    {
        switch ($taxon->getCode()) {
            case 'audio':
                return 'https://www.bimmer-tech.net/speakers-and-audio-amplifier-for-bmw';
            case 'idrive':
                return 'https://www.bimmer-tech.net/bmw-idrive-coding';
            default:
                return '/category-' . $taxon->getSlug();
        }
    }
}

