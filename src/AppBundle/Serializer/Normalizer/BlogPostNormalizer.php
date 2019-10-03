<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class BlogPostNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class BlogPostNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $result = [];
        foreach ($object as $blogPostObject) {
            if ($blogPostObject->getEnabled()) {
                /** @var  BlogPost $blogPostObject */
                $result[] = [
                    'id' => $blogPostObject->getId(),
                    'title' => $blogPostObject->getTitle(),
                    'category_name' => $blogPostObject->getBlogCategory() ?
                        $blogPostObject->getBlogCategory()->getName() : null,
                    'date' => $blogPostObject->getCreatedAt()->format('Y-m-d H:i:s'),
                    'enable' => $blogPostObject->getEnabled(),
                    'slug' => $blogPostObject->getSlug(),
                    'author' => $blogPostObject->getAuthor(),
                    'seoText'=> $blogPostObject->getSeoText(),
                    'thumbnail' => $blogPostObject->getBlogPostImage()->count() > 0 ?
                        $this->getThumbnail($blogPostObject->getBlogPostImage())
                        : $this->getThumbnail([]),
                ];
            }
        }

        return $result;
    }

    /**
     * @param $images
     * @return array
     */
    public function getThumbnail($images)
    {
        $arrayImages = [
            'twoToOne' => null,
            'oneToOne' => null,
        ];
        /** @var BlogPostImage $image */
        foreach ($images as $image) {
            if ($image->getAspectRatio() == BlogPostImage::ASPECT_RATIO_TWO_TO_ONE) {
                $arrayImages['twoToOne'] = 'media/image/' . $image->getPath();
            } elseif ($image->getAspectRatio() == BlogPostImage::ASPECT_RATIO_ONE_TO_ONE) {
                $arrayImages['oneToOne'] = 'media/image/' . $image->getPath();
            }
        }

        return $arrayImages;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BlogPost;
    }
}
