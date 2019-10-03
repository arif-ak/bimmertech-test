<?php

namespace AppBundle\Repository;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class WarehouseRepository
 * @package AppBundle\Repository
 */
class MediaImageRepository extends EntityRepository
{
    /**
     * @param int $count
     * @param int $offSet
     * @param string $sort
     * @param string $order
     * @return mixed
     */
    public function getByFilter($count = 1, $offSet = 1, $sort = 'id', $order = 'DESC')
    {
        return
            $this->createQueryBuilder('i')
                ->orderBy('i.' . $sort, $order)
                ->setMaxResults($count)
                ->setFirstResult(($offSet - 1)*$count)
                ->getQuery()
                ->getResult();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function getByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('i');
        return $qb->add('where', $qb->expr()->in('i.id', $ids))
            ->getQuery()
            ->getResult();
    }
}
