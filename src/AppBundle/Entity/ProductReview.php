<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\ProductReview as BaseProductReview;
use Sylius\Component\Review\Model\ReviewableInterface;

class ProductReview extends BaseProductReview
{
    protected $reviewLike;

//    protected $reviewSubject;

    /**
     * @return mixed
     */
    public function getReviewLike()
    {
        return $this->reviewLike;
    }

    /**
     * @param mixed $reviewLike
     */
    public function setReviewLike($reviewLike): void
    {
        $this->reviewLike = $reviewLike;
    }

//    /**
//     */
//    public function getReviewSubject()
//    {
//        return $this->reviewSubject;
//    }
//
//    /**
//     * @param $reviewSubject
//     */
//    public function setReviewSubject($reviewSubject): void
//    {
//        $this->reviewSubject = $reviewSubject;
//    }
}
