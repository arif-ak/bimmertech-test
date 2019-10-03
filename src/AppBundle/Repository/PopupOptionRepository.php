<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PopupOptionRepository extends EntityRepository
{
    public function findByProductId($productId)
    {

        $qb = $this->createQueryBuilder('po')
            ->select('po')
//            ->leftJoin('po.popupOptionProduct', 'pr')
//            ->where('pr.id = :productId')
//            ->setParameter('productId', $productId)
            ->getQuery();

        return $qb->getResult();
    }
}
