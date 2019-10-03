<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class InterestingInCompatible
 * @package AppBundle\Service
 */
class InterestingInCompatible
{

    /**
     * Filter compatible products
     *
     * @param array $products
     * @param $session
     * @return array
     */
    public function getCompatible(array $products, $session)
    {
        if ($vinCheck = $session->get('vincheck')) {

            $vinProducts = (new ArrayCollection($vinCheck['products']))->map(function ($item) {
                return $item['productId'];
            })->toArray();

            $products = (new ArrayCollection($products))->filter(function ($product) use ($vinProducts) {
                /** @var Product $product */
                if (in_array($product->getId(), $vinProducts) && $product->isEnabled()) {
                    return $product;
                }
            })->toArray();

            return $products;
        }
        $products = (new ArrayCollection($products))->filter(function ($product) {
            /** @var Product $product */
            if ($product->isEnabled()) {
                return $product;
            }
        })->toArray();
        return $products;
    }
}