<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use AppBundle\Entity\BlogPostProduct;
use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductReviews
 * @package AppBundle\Serializer\Normalizer
 */
class  AdminBlogPostNormalizer implements NormalizerInterface
{
    /**
     * @param BlogPost $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $category = [
            'enabled' => true,
            'name' => 'No category',
            'id' => 0
        ];

        if ($object->getBlogCategory()) {
            $category = [
                'enabled' => $object->getBlogCategory()->getEnabled(),
                'name' => $object->getBlogCategory()->getName(),
                'id' => $object->getBlogCategory()->getId()
            ];
        }

        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'category' => $category,
            'date' => $object->getCreatedAt()->format('Y-m-d H:m:s'),
            'enabled' => $object->getEnabled(),
            'slug' => $object->getSlug(),
            'metaDescription' => $object->getMetaDescription(),
            'metaKeywords' => $object->getMetaKeywords(),
            'metaTags' => $object->getMetaTags(),
            'seoText'=>$object->getSeoText(),
            'content' => $object->getContent(),
            'thumbnail' => $object->getBlogPostImage()->count() > 0 ? $this->getThumbnail($object->getBlogPostImage()) : $this->getThumbnail([]),
//            'relatedProducts' => $this->getRelatedProducts($object),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail($images)
    {
        $arrayImages = [
            'twoToOne' => null,
            'oneToOne' => null,
        ];
        /** @var BlogPostImage $image */
        foreach ($images as $image) {
            if ($image->getAspectRatio() == '2:1') {
                $arrayImages['twoToOne'] = $image->getPath();
            } elseif ($image->getAspectRatio() == "1:1") {
                $arrayImages['oneToOne'] = $image->getPath();
            }
        }
        return $arrayImages;
    }

    /**
     * @param BlogPost $blogPost
     * @return array
     */
    public function getRelatedProducts(BlogPost $blogPost): array
    {
        $array = [];
        /** @var Product $product */
        foreach ($blogPost->getProductRelated() as $item) {
            /** @var BlogPostProduct $item */
            $product = $item->getProduct();
            $array[] = [
                'id' => $product->getId(),
                'code' => $product->getCode(),
                'name' => $product->getName()
            ];
        }
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BlogPost;
    }
}
