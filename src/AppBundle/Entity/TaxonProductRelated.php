<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

class TaxonProductRelated implements ResourceInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Taxon
     */
    protected $category;

    /**
     * @var
     */
    protected $products;

    /**
     * TaxonProductRelated constructor.
     */
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
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return Taxon
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Taxon $category
     */
    public function setCategory(Taxon $category = null): void
    {
        $this->category = $category;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        if (false == $this->products->contains($product)) {
            $this->products->add($product);
        }
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        if (true == $this->products->contains($product)){
            $this->products->removeElement($product);
        }
    }
}
