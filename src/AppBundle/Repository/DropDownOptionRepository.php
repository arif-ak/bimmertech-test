<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class DropDownOptionRepository
 *
 * @package AppBundle\Repository
 */
class DropDownOptionRepository extends EntityRepository
{
    /**
     * Get drop down option by ids
     *
     * @param array $ids
     * @return mixed
     */
    public function getDropDownOptionByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('o');
        return $qb->add('where', $qb->expr()->in('o.id', $ids))
            ->getQuery()
            ->getResult();
    }
}