<?php

namespace AppBundle\Repository;

use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

class TaxonRepository extends BaseTaxonRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function getTaxonContainers($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = 1')
            ->andWhere('o.parent = :id')
            ->andWhere('o.level = 2')
            ->setParameter('id', $id)
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $level
     * @return mixed
     */
    public function getEnabledByLevel($level)
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = 1')
            ->andWhere('o.level = :level')
            ->setParameter('level', $level)
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function orderByPosition()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.level = :level')
            ->setParameter('level', 1)
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param string $slug
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findContainerBySlug(string $slug)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.slug = :slug')
            ->andWhere('o.level = 2')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $taxonId
     * @param $channel
     * @return mixed
     */
    public function taxonRating($taxonId, $channel)
    {
        $dql = $this->createQueryBuilder('o')
            ->select(
                [
                    'o',
                    'count(r) as reviewCount',
                    'avg(r.rating) as avg_rating',
                    'sum(case when r.rating = 1 then 1 else 0 end) as rating_1',
                    'sum(case when r.rating = 2 then 1 else 0 end) as rating_2',
                    'sum(case when r.rating = 3 then 1 else 0 end) as rating_3',
                    'sum(case when r.rating = 4 then 1 else 0 end) as rating_4',
                    'sum(case when r.rating = 5 then 1 else 0 end) as rating_5'
                ]
            )
            ->leftJoin('o.reviews', 'r')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.mainTaxon = :taxonId')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('taxonId', $taxonId);

        $dql->groupBy('o.id')
            ->orderBy('o.recomended', "DESC");

        return $dql->getQuery()->getResult();
    }
}
