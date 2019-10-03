<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository as BaseRepository;

/**
 * Class ProductVariantRepository
 * @package AppBundle\Repository
 */
class ProductVariantRepository extends BaseRepository
{

    /**
     * Get product variant by ids
     *
     * @param array $ids
     * @return mixed
     */
    public function getProductVariantsByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->add('where', $qb->expr()->in('p.id', $ids))
            ->getQuery()
            ->getResult();
    }

    /**
     * Get product variant by ids
     *
     * @param array $ids
     * @return mixed
     */
    public function getProductVariantsByProductIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->add('where', $qb->expr()->in('p.product', $ids))
            ->getQuery()
            ->getResult();
    }

    /**
     * Get product variant by ids
     *
     * @param array $ids
     * @return mixed
     */
    public function fingByVinCheckIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->add('where', $qb->expr()->in('p.vincheckserviceId', $ids))
            ->getQuery()
            ->getResult();
    }
}