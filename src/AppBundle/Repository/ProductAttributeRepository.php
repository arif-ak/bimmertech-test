<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductAssociation;

/**
 * Class ProductVariantRepository
 * @package AppBundle\Repository
 */
class ProductAttributeRepository extends EntityRepository
{
//    /**
//     * {@inheritdoc}
//     */
//    public function createListQueryBuilder(): QueryBuilder
//    {
//
//
//        return $this->createQueryBuilder('o')
//            ->innerJoin('o.translations', 'translation')
//            ->andWhere('translation.locale = :locale')
//            ->setParameter('locale', $locale)
//            ;
//    }
}