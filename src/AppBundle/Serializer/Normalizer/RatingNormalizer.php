<?php

namespace AppBundle\Serializer\Normalizer;

use Sylius\Component\Core\Model\ProductReview;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductReviews
 * @package AppBundle\Serializer\Normalizer
 */
class RatingNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $rating
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($rating, $format = null, array $context = array())
    {
        return [
            'ratings' => [
                count($rating) > 0 ? (int)$rating[0]['rating_5'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_4'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_3'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_2'] : 0,
                count($rating) > 0 ? (int)$rating[0]['rating_1'] : 0,
            ],
            'averageRating' => count($rating) > 0 ? (int)$rating[0]['avg_rating'] : 0,
            'reviewCount' => count($rating) > 0 ? (int)$rating[0]['reviewCount'] : 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductReview;
    }
}
