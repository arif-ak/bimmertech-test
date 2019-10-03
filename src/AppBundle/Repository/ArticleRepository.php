<?php

namespace AppBundle\Repository;

use Webburza\Sylius\ArticleBundle\Doctrine\ORM\ArticleRepository as BaseArticleRepository;

/**
 * Class ArticleRepository
 * @package AppBundle\Repository
 */
class ArticleRepository extends BaseArticleRepository
{
    /**
     * @param int $count
     * @return array
     */
    public function findHomeBlog(int $count): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.published = true')
            ->andWhere('o.homepage = true')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }
}
