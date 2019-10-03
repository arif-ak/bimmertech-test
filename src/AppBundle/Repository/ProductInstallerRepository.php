<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class SlideRepository
 * @package AppBundle\Repository
 */
class ProductInstallerRepository extends EntityRepository
{

    public function getByProductId($productId)
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.product', 'product', 'WITH', 'product.id = :id')
            ->setParameter('id', $productId)
            ->getQuery()
            ->getResult();

    }
}