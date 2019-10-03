<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ProductReview;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductReviewRepository as BaseProductReviewRepository;
use Sylius\Component\Review\Model\ReviewInterface;

class ProductReviewRepository extends BaseProductReviewRepository
{
    public function pagination($productId, $page = null)
    {
        if ($page == 0) {
            $limit = 3;
            $fromResult = $page * $limit;
        } else {
            $limit = 5;
            $fromResult = $page * $limit;
            $fromResult = $fromResult - 2;
        }

        return $this->createQueryBuilder('o')
            ->select('o', 'count(rl) as likeCount')
            ->leftJoin("o.reviewLike", "rl")
            ->andWhere('o.reviewSubject = :productId')
            ->andWhere('o.status = :status')
            ->setParameter('productId', $productId)
            ->setParameter('status', ReviewInterface::STATUS_ACCEPTED)
            ->addOrderBy('o.createdAt', 'DESC')
            ->setFirstResult($fromResult)
            ->setMaxResults($limit)
            ->groupBy('o.id')
            ->orderBy('o.id', "desc")
            ->getQuery()
            ->getResult();
    }

    public function starRatingByProduct($id)
    {
        $class = ProductReview::class;
        $dql = $this->_em->createQuery("select
            count(r) as reviewCount,
            avg(r.rating) as avg_rating,
            sum(case when r.rating = 1 then 1 else 0 end) as rating_1,
            sum(case when r.rating = 2 then 1 else 0 end) as rating_2,
            sum(case when r.rating = 3 then 1 else 0 end) as rating_3,
            sum(case when r.rating = 4 then 1 else 0 end) as rating_4,
            sum(case when r.rating = 5 then 1 else 0 end) as rating_5
        from
           ".$class." r where r.reviewSubject=".$id." AND r.status='accepted'");
        return $dql->getArrayResult();
    }
}
