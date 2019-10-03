<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Symfony\Component\DependencyInjection\Container;

class DeleteProduct
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function deleteProduct(Product $product)
    {
        $productRepository = $this->container->get('sylius.repository.product');

        $orderItems = $this->container->get('sylius.repository.order_item')->finWhereUsedProduct($product);

        if ($associations = $this->getAssociationMessage($product)) {
            return $associations;
        }
        return;
    }

    /**
     * @param Product $product
     * @return bool|string
     * @throws \Exception
     */
    private function getAssociationMessage(Product $product)
    {
        $associations = $this->container->get('sylius.repository.product_association')->finUsed($product);
        foreach ($associations as $item) {
            return 'This product used in product id = ' . $item->getId();
        }
        return false;
    }
}