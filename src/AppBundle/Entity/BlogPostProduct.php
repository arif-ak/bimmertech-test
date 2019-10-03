<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class BlogPostProduct
 *
 * @package AppBundle\Entity
 */
class BlogPostProduct implements ResourceInterface
{
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $products;

    /**
     * @var
     */
    protected $blogPost;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $product
     */
    public function addProduct($product)
    {
        if (false == $this->products->contains($product)) {
            $this->products->add($product);
        }
    }

    /**
     * @param $product
     */
    public function removeProduct($product)
    {
        if (true == $this->products->contains($product)) {
            $this->products->removeElement($product);
        }
    }

    /**
     * @return mixed
     */
    public function getBlogPost()
    {
        return $this->blogPost;
    }

    /**
     * @param mixed $blogPost
     */
    public function setBlogPost($blogPost): void
    {
        $this->blogPost = $blogPost;
    }
}
