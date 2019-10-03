<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Stripe\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class BlogPost
 *
 * @package AppBundle\Entity
 */
class BlogPost implements ResourceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $counter;

    /**
     * @var BlogCategory
     */
    protected $blogCategory;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $metaKeywords;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var string
     */
    protected $metaTags;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var  Collection
     */
    protected $blogPostImage;

    /**
     * @var  Collection
     */
    protected $blogPostContent;

    /*
     * @var string
     */
    protected $content;

    /*
     * @var string
     */
    protected $author;

    /**
     * @var ArrayCollection
     */
    protected $productRelated;

    /**
     * @var ArrayCollection
     */
    protected $postReviews;

    /**
     * @var string
     */
    protected $seoText;

    /**
     * BlogPost constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');

        $this->blogPostImage = new ArrayCollection();
        $blogPostImage1 = new BlogPostImage();
        $blogPostImage1->setAspectRatio(BlogPostImage::ASPECT_RATIO_ONE_TO_ONE);
        $blogPostImage2 = new BlogPostImage();
        $blogPostImage2->setAspectRatio(BlogPostImage::ASPECT_RATIO_TWO_TO_ONE);
        $this->addBlogPostImage($blogPostImage1);
        $this->addBlogPostImage($blogPostImage2);
        $this->postReviews = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getBlogCategory()
    {
        return $this->blogCategory;
    }

    /**
     * @param mixed $blogCategory
     */
    public function setBlogCategory($blogCategory): void
    {
        $this->blogCategory = $blogCategory;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords(string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    /**
     * @param mixed $metaTags
     */
    public function setMetaTags($metaTags): void
    {
        $this->metaTags = $metaTags;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return ArrayCollection
     */
    public function getBlogPostImage()
    {
        return $this->blogPostImage;
    }

    /**
     * @return bool
     */
    public function hasBlogPostImage(): bool
    {
        return !$this->blogPostImage->isEmpty();
    }

    /**
     * @param BlogPostImage $blogPostImage
     */
    public function addBlogPostImage(BlogPostImage $blogPostImage)
    {
        if (true === $this->blogPostImage->contains($blogPostImage)) {
            return;
        }
        $this->blogPostImage->add($blogPostImage);
        $blogPostImage->setBlogPost($this);
        $blogPostImage->setOwner($this);
    }

    /**
     * @param BlogPostImage $blogPostImage
     */
    public function removeBlogPostImage(BlogPostImage $blogPostImage)
    {
        if (false === $this->blogPostImage->contains($blogPostImage)) {
            return;
        }

        $this->blogPostImage->removeElement($blogPostImage);
        $blogPostImage->setBlogPost(null);
        $blogPostImage->setOwner(null);
    }

    /**
     * @param BlogPostImage $blogPostImage
     */
    public function removeAllBlogPostImage()
    {
        foreach ($this->blogPostImage as $item) {
            $this->blogPostImage->removeElement($item);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getProductRelated()
    {
        return $this->productRelated;
    }

//    /**
//     * @param BlogPostProduct $relatedProduct
//     */
//    public function addProductRelated(BlogPostProduct $relatedProduct)
//    {
//        if (true === $this->productRelated->contains($relatedProduct)) {
//            return;
//        }
//
//        $this->productRelated->add($relatedProduct);
//        $relatedProduct->setBlogPost($this);
//    }
//
//    /**
//     * @param BlogPostProduct $relatedProduct
//     */
//    public function removeProductRelated(BlogPostProduct $relatedProduct)
//    {
//        if (false === $this->productRelated->contains($relatedProduct)) {
//            return;
//        }
//        $this->productRelated->removeElement($relatedProduct);
//    }

//    /**
//     * Remove all products related
//     */
//    public function removeProductRelatedAll(): void
//    {
//        foreach ($this->productRelated as $item) {
//            $this->productRelated->removeElement($item);
//        }
//    }

    /**
     * @return int
     */
    public function getCounter(): ?int
    {
        return $this->counter;
    }

    /**
     * @param int $counter
     */
    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return BlogPost|ArrayCollection
     */
    public function getPostReviews()
    {
        return $this - $this->postReviews;
    }

    /**
     * @param BlogReview $blogReview
     */
    public function addPostReview(BlogReview $blogReview)
    {
        if (true === $this->postReviews->contains($blogReview)) {
            return;
        }

        $this->postReviews->add($blogReview);
        $blogReview->setReviewSubject($this);
    }

    /**
     * @param BlogReview $blogReview
     */
    public function removePostReview(BlogReview $blogReview)
    {
        if (false === $this->postReviews->contains($blogReview)) {
            return;
        }
        $this->postReviews->removeElement($blogReview);
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getSeoText(): ?string
    {
        return $this->seoText;
    }

    /**
     * @param $seoText
     */
    public function setSeoText($seoText): void
    {
        $this->seoText = $seoText;
    }
}
