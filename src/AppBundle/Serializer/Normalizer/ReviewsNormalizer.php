<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\BlogReview;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductReviews
 * @package AppBundle\Serializer\Normalizer
 */
class ReviewsNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $reviews = $object;

        return array_map(function ($review) {
            $reviewObject = $review[0];
            $likeCount = (int)$review['likeCount'];

            if ($reviewObject->getStatus() === ReviewInterface::STATUS_ACCEPTED) {
                return [
                    'id' => $reviewObject->getId(),
                    'rating' => $reviewObject->getRating(),
                    'title' => $reviewObject->getTitle(),
                    'comment' => $reviewObject->getComment(),
                    'date' => $reviewObject->getCreatedAt()->getTimestamp(),
                    'user' => [
                        'name' => $reviewObject->getAuthor()->getFirstName(),
                        'last_name' => $reviewObject->getAuthor()->getLastName(),
                        'email' => $reviewObject->getAuthor()->getEmail(),
                        'image' => $reviewObject->getAuthor()->getPath() ?
                            '/media/image/' . $reviewObject->getAuthor()->getPath() : null
                    ],
                    'likeCount' => $likeCount
                ];
            }
        }, $reviews);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductReview || $data instanceof BlogReview;
    }
}
