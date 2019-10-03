<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductAssociation;

/**
 * Class ProductVariantRepository
 * @package AppBundle\Repository
 */
class ProductAssociationRepository extends EntityRepository
{
    /**
     * @param $product
     * @return array
     */
    public function finUsed($product):array
    {
        $products = [];
        $result = $this->createQueryBuilder('a')
            ->join('a.associatedProducts', 'p')
            ->andWhere('p = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getResult();
        /** @var ProductAssociation $item */
        foreach ($result as $item) {
            $products [] = $item->getOwner();
        }
        return $products;
    }
}