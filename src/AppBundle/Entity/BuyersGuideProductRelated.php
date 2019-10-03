<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class BuyersGuideProductOption
 * @ORM\Table(name="app_buyers_guide_product_related")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuyersGuideProductRelatedRepository")
 */
class BuyersGuideProductRelated implements ResourceInterface
{
    /**
     * @var $id
     */
    protected $id;

    /**
     * @var Product
     */
    protected $currentProduct;

    /**
     * @var Product
     */
    protected $relatedProduct;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->currentProduct;
    }

    /**
     * @param $currentProduct
     */
    public function setCurrentProduct($currentProduct): void
    {
        $this->currentProduct = $currentProduct;
    }

    /**
     */
    public function getRelatedProduct()
    {
        return $this->relatedProduct;
    }

    /**
     * @param Product $relatedProduct
     */
    public function setRelatedProduct( $relatedProduct): void
    {
        $this->relatedProduct = $relatedProduct;
    }
}
