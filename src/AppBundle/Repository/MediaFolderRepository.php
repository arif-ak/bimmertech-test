<?php


namespace AppBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class MediaFolderRepository
 * @package AppBundle\Repository
 */
class MediaFolderRepository extends EntityRepository
{
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