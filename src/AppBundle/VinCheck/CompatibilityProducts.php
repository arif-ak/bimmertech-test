<?php

namespace AppBundle\VinCheck;

use AppBundle\Entity\BuyersGuideProductRelated;
use AppBundle\Entity\PopupOption;
use AppBundle\Entity\PopupOptionInterface;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Taxon;
use ArrayIterator;
use Sylius\Component\Product\Model\ProductAssociation;

/**
 * Class CompatibilityProducts
 * @package AppBundle\VinCheck
 */
class CompatibilityProducts
{
    /**
     * @param $resources
     * @param array $sessionProducts
     */
    public function setAllCompatibility(&$resources, array $sessionProducts, $compatibility)
    {
        if (!is_array($resources) && method_exists($resources, 'getData')) {
            /** @var ArrayIterator $productsIterator */
            $productsIterator = $resources->getData()->getCurrentPageResults();
            $productsIterator->rewind();

            while ($productsIterator->valid()) {
                $product = $productsIterator->current();
                $this->productCompatibility($product, $sessionProducts, $compatibility);
                $productsIterator->next();
            }
        } else {
            /** @var Product $product */
            foreach ($resources as $product) {
                $this->productCompatibility($product, $sessionProducts, $compatibility);
            }
        }
    }

    /**
     * @param $resource
     * @param array $sessionProducts
     */
    public function setOneCompatibility(&$resource, array $sessionProducts, $compatibility)
    {
        $this->productCompatibility($resource, $sessionProducts, $compatibility, true);
    }

    /**
     * @param Product $product
     * @param $sessionProducts
     */
    private function productCompatibility(Product &$product, $sessionProducts, $compatibility, $checkAddons = false)
    {
        $product->setCompatibility(ProductInterface::COMPATIBILITY_NO);
        foreach ($sessionProducts as $sessionProduct) {
            if ($product->getCode() === $sessionProduct['code']) {
                $product->setCompatibility($compatibility);
                if ($checkAddons) {
                    $this->checkAddons($product, $sessionProduct, $compatibility);
                }
            }
        }

        if ($product->getBuyersRelated()->count() > 0) {
            $this->buyersGuideProductCompatibility($product, $sessionProducts, $compatibility);
        }
    }

    /**
     * @param Product $product
     * @param $sessionProduct
     */
    private function checkAddons(Product &$product, $sessionProduct, $compatibility)
    {
        $addons = null;
        /** @var ProductAssociation $association */
        foreach ($product->getAssociations() as $association) {
            if ($association->getType()->getCode() === 'Addons') {
                $addons = $association->getAssociatedProducts();
            }
        }

        if ($addons !== null) {
            /** @var Product $addon */
            foreach ($addons as $addon) {
                $addon->setCompatibility(ProductInterface::COMPATIBILITY_NO);
                foreach ($sessionProduct['addons'] as $sessionAddon) {
                    if ($addon->getId() === $sessionAddon['productId']) {
                        $addon->setCompatibility($compatibility);
                    }
                }
            }
        }
    }

    public function buyersGuideProductCompatibility(Product &$product, $sessionProducts, $compatibility)
    {
        /** @var BuyersGuideProductRelated $relProduct */
        foreach ($product->getBuyersRelated() as $relProduct) {

           if ( $productRel = $relProduct->getRelatedProduct()){
               $productRel->setCompatibility(ProductInterface::COMPATIBILITY_NO);
               foreach ($sessionProducts as $sessionProduct) {
                   if ($productRel->getCode() === $sessionProduct['code']) {
                       $productRel->setCompatibility($compatibility);
                   }
               }
           }
        }
    }

    public function popupOptionCompatibility(Product &$product, $sessionPopupOptions)
    {
        /** @var PopupOption $popupOption */
        foreach ($product->getProductPopupOption() as $popupOption) {
            $popupOption->setCompatibility(PopupOptionInterface::COMPATIBILITY_NO);
            if (!empty($popupOption->getVinCheckServiceId())) {
                foreach ($sessionPopupOptions as $sessionPopupOption) {
                    if ($popupOption->getVinCheckServiceId() === $sessionPopupOption['id']) {
                        $popupOption->setCompatibility($sessionPopupOption['compatibility']);
                    }
                }
            }
        }
    }

    /**
     * @param Taxon $taxon
     * @param $sessionPopupOptions
     */
    public function taxonPopupOptionCompatibility(Taxon &$taxon, $sessionPopupOptions)
    {
        /** @var PopupOption $popupOption */
        foreach ($taxon->getPopupOption() as $popupOption) {
            $popupOption->setCompatibility(PopupOptionInterface::COMPATIBILITY_NO);
            if (!empty($popupOption->getVinCheckServiceId())) {
                foreach ($sessionPopupOptions as $sessionPopupOption) {
                    if ($popupOption->getVinCheckServiceId() === $sessionPopupOption['id']) {
                        $popupOption->setCompatibility($sessionPopupOption['compatibility']);
                    }
                }
            }
        }
    }

    /**
     * @param $products
     * @param array $sessionProducts
     * @param $compatibility
     * @return Product|null
     */
    public function compatibilityContainer(&$products, array $sessionProducts, $compatibility)
    {
        /** @var Product $product */
        foreach ($products as $product) {
            $this->productCompatibility($product, $sessionProducts, $compatibility);
            if ($product->getCompatibility() !== ProductInterface::COMPATIBILITY_NO)
                return $product;
        }
        return null;
    }

    /**
     * Check product is compatibility
     *
     * @param Product $product
     * @param array $vinData
     * @return mixed|string
     */
    public function checkCompatibilityProduct(&$product, array $vinData)
    {
        foreach ($vinData['products'] as $item) {
            if ($product->getId() === $item['productId'] && $item['code']) {
                return $vinData['compatibility'];
            }
        }

        return 'No';
    }

    /**
     * @param $resources
     * @param array $sessionProducts
     */
    public function setCategoryList(&$resources, array $sessionProducts, $compatibility)
    {
        /** @var Product $product */
        foreach ($resources as $product) {
            $this->productCompatibility($product[0], $sessionProducts, $compatibility);
        }
    }

    /**
     * @param $resource
     * @param array $sessionProducts
     */
    public function setCategoryListOneCompatibility(&$resource, array $sessionProducts, $compatibility)
    {
        $this->productCompatibility($resource[0], $sessionProducts, $compatibility, true);
    }
}
