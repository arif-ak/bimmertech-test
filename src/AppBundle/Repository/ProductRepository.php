<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

/**
 * Class ProductRepository
 * @package AppBundle\Repository
 */
class ProductRepository extends BaseProductRepository
{
    /**
     * @var AssociationHydrator
     */
    protected $associationsHydrator;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManager $entityManager, Mapping\ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationsHydrator = new \AppBundle\Doctrine\AssociationHydrator($entityManager, $class);
    }

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @return mixed
     */
    public function getProductByChannel(ChannelInterface $channel, string $locale)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addGroupBy('o.id')
            ->setParameter('locale', $locale)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     * @return mixed
     */
    public function getProductByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon)
    {
        return $this->createQueryBuilder('p')
            ->andWhere(':channel MEMBER OF p.channels')
            ->andWhere('p.mainTaxon = :taxon')
            ->andWhere('p.enabled = true')
            ->addGroupBy('p.id')
            ->setParameter('taxon', $taxon)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param int $count
     * @return array
     */
    public function findRecommendedByChannel(ChannelInterface $channel, string $locale, int $count): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.recomended = true')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param array $hydrations
     *
     * @return mixed
     */
    public function findByIdsAndHydrate(array $ids, array $hydrations = [], array $wheres = [], ChannelInterface $channel, string $locale)
    {
        $products = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.id IN (:ids)')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('ids', implode(', ', $ids));

        foreach ($wheres as $key => $value) {
            $products = $products->andWhere("o.$key = '$value'");
        }

        $products = $products->getQuery()->getResult();


        if (!empty($hydrations)) {
            $this->associationsHydrator->hydrateAssociations($products, $hydrations);
        }

        return $products;
    }

    public function findAssociationByCode($id, $code)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.associations', 'associations', 'WITH', 'associations.id = :code')
            ->setParameter('code', 24)
            ->getQuery()
            ->getResult();
    }

    public function productPagination($taxonId, $channel, $page = null, $taxonCount = null)
    {
        $showAll = true;
        $fromResult = 0;

        if ($page == null) {
            $resultWhenPageIsNull = 8;
            $limit = $resultWhenPageIsNull - $taxonCount;
            $limit = $limit > 0 ? $limit : 0;

            $showAll = false;
        }

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

        if (!$showAll) {
            $dql->setFirstResult($fromResult)
                ->setMaxResults($limit);
        }

        $dql->groupBy('o.id')
            ->orderBy('o.recomended', "DESC");

        return $dql->getQuery()->getResult();
    }

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

        $dql->groupBy('o.mainTaxon')
            ->orderBy('o.recomended', "DESC");
        return $dql->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByNamePartLimit(string $phrase, string $locale, int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere('translation.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->setParameter('locale', $locale)
            ->setMaxResults($limit)
            ->orderBy('translation.name','asc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function findByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('enabled= true');
        return $qb->add('where', $qb->expr()->in('p.id', $ids))
            ->getQuery()
            ->getResult();
    }
}
