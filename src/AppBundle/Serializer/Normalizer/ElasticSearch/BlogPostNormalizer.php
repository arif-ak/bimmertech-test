<?php

namespace AppBundle\Serializer\Normalizer\ElasticSearch;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductReviews
 * @package AppBundle\Serializer\Normalizer
 */
class BlogPostNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $posts
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($posts, $format = null, array $context = array())
    {
        $showAll = isset($context['showAll']) ? $context['showAll'] : false;
        $count = !$showAll ? 2 : count($posts);

        $postsArray = [];
        /** @var BlogPost $post */
        foreach ($posts as $key => $post) {
            if ($key < $count) {
                $postsArray[] = [
                    'id' => $post->getId(),
                    'slug' => '/blog/item/' . $post->getSlug(),
                    'title' => $post->getTitle(),
                    'category' => $post->getBlogCategory() ? $post->getBlogCategory()->getName() : null,
                    'image_path' => $this->getBlogImage($post->getBlogPostImage()->toArray(), $showAll)
                ];
            }
        }

        return $postsArray;
    }

    /**
     * @param $images
     * @param bool $showAll
     * @return array|null|string
     */
    public function getBlogImage($images, $showAll = false)
    {
        $imagesArray = [];

        if (count($images) > 0) {
            /** @var BlogPostImage $image */
            foreach ($images as $image) {
                if ($image->getAspectRatio() == BlogPostImage::ASPECT_RATIO_ONE_TO_ONE && !$showAll) {
                    return '/media/image/' . $image->getPath();
                } elseif ($image->getAspectRatio() == BlogPostImage::ASPECT_RATIO_ONE_TO_ONE && $showAll) {
                    $imagesArray[BlogPostImage::ASPECT_RATIO_ONE_TO_ONE] =
                        '/media/image/' . $image->getPath();
                } elseif ($image->getAspectRatio() == BlogPostImage::ASPECT_RATIO_TWO_TO_ONE && $showAll) {
                    $imagesArray[BlogPostImage::ASPECT_RATIO_TWO_TO_ONE] =
                        '/media/image/' . $image->getPath();
                }
            }

            return $imagesArray;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BlogPost;
    }
}
