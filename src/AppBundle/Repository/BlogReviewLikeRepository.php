<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ProductReview;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductReviewRepository as BaseProductReviewRepository;
use Sylius\Component\Review\Model\ReviewInterface;

class BlogReviewLikeRepository extends EntityRepository
{
    /**
     * @param $reviewId
     * @param $clientToken
     * @return mixed
     */
    public function findByClientTokenAndReview($reviewId, $clientToken)
    {
        return $this->createQueryBuilder('l')
            ->where('l.review = :review')
            ->andWhere('l.clientToken = :clientToken')
            ->setParameter('review', $reviewId)
            ->setParameter('clientToken', $clientToken)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $reviewId
     * @param $clientToken
     * @return mixed
     */
    public function findByCustomerAndReview($reviewId, $customerId, $clientToken = null)
    {
        return $this->createQueryBuilder('l')
            ->where('l.review = :review')
            ->andWhere('l.customer = :customer OR l.clientToken = :clientToken')
            ->setParameter('review', $reviewId)
            ->setParameter('customer', $customerId)
            ->setParameter('clientToken', $clientToken)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $clientToken
     * @param $productId
     * @return mixed
     */
    public function findByClientToken($clientToken, $productId = null)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin("l.review", "r")
            ->where('l.clientToken = :clientToken')
            ->andWhere('r.reviewSubject = :productId')
            ->setParameter('clientToken', $clientToken)
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $customerId
     * @param null $clientToken
     * @param null $productId
     * @return mixed
     */
    public function findByCustomer($customerId, $clientToken = null, $productId = null)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin("l.review", "r")
            ->where('l.customer = :customer OR l.clientToken = :clientToken')
            ->andWhere('r.reviewSubject = :productId')
            ->setParameter('productId', $productId)
            ->setParameter('clientToken', $clientToken)
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getResult();
    }
}
