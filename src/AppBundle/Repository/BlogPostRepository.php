<?php

namespace AppBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class BlogPostRepository
 * @package AppBundle\Repository
 */
class BlogPostRepository extends EntityRepository
{
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $tags
     * @param $id
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findRelatedPosts($tags, $id)
    {
        $dql = $this->createQueryBuilder('p');

        $length = count($tags);
        foreach ($tags as $key => $tag) {
            if ($key == 0) {
                $dql->andWhere("p.metaTags LIKE '%$tag%'");
            } else {
                $dql->orWhere("p.metaTags LIKE '%$tag%'");
            }
        }

        $dql->andWhere('p.id != :id')
            ->setParameter('id', $id)
            ->andWhere('p.enabled = true')
            ->orderBy('p.createdAt', "DESC")
            ->setMaxResults(3);

        return $dql->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getLatest()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.enabled = true')
            ->orderBy('p.createdAt', "DESC")
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function getLatestMainPage($result = 3)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($result)
            ->andWhere('p.enabled = true')
            ->orderBy('p.counter', "DESC")
            ->getQuery()
            ->getResult();
    }
}
