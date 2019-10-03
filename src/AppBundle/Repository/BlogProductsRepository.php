<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BlogProductsRepository extends EntityRepository
{
    public function getInterestedIn()
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults(4)
            ->orderBy('p.id', "DESC")
            ->getQuery()
            ->getResult();
    }
}
