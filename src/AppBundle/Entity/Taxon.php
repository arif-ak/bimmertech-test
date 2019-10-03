<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

/**
 * Class Taxon
 * @package AppBundle\Entity
 */
class Taxon extends BaseTaxon
{
    const TAXON_CONTAINER = 2;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var
     */
    protected $price;

    /**
     * @var bool
     */
    protected $isContainer = false;

    /**
     * @var ArrayCollection
     */
    protected $products;

    /**
     * @var
     */
    protected $shortDescription;

    /**
     * @var
     */
    protected $productName;

    /**
     * @var
     */
    protected $compatibility;

    /**
     * @var ArrayCollection
     */
    protected $popupOption;

    /**
     * @var
     */
    protected $bestseller;

    /**
     * @var
     */
    protected $metaTitle;

    /**
     * @var
     */
    protected $seoText;

    /**
     * @var
     */
    protected $metaKeywords;

    /**
     * @var
     */
    protected $metaDescription;

    /**
     * @var
     */
    protected $productRelated;

    /**
     * @var
     */
    protected $teaser;

    /**
     * Taxon constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->products = new ArrayCollection();
        $this->popupOption = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isContainer(): bool
    {
        return $this->isContainer;
    }

    /**
     * @param bool $isContainer
     */
    public function setIsContainer(bool $isContainer)
    {
        $this->isContainer = $isContainer;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return Collection
     */
    public function getProducts(): ?Collection
    {

        return $this->products;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function addProducts(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * @param Product $product
     */
    public function removeProducts(Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param mixed $shortDescription
     */
    public function setShortDescription($shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param mixed $productName
     */
    public function setProductName($productName): void
    {
        $this->productName = $productName;
    }

    /**
     * @return mixed
     */
    public function getCompatibility()
    {
        return $this->compatibility;
    }

    /**
     * @param mixed $compatibility
     */
    public function setCompatibility($compatibility): void
    {
        $this->compatibility = $compatibility;
    }

    /**
     * @return mixed
     */
    public function getPopupOption()
    {
        return $this->popupOption;
    }

    /**
     * @param mixed $popupOption
     */
    public function setPopupOption($popupOption): void
    {
        $this->popupOption = $popupOption;
    }

    /**
     * @return mixed
     */
    public function getBestseller()
    {
        return $this->bestseller;
    }

    /**
     * @param mixed $bestseller
     */
    public function setBestseller($bestseller): void
    {
        $this->bestseller = $bestseller;
        $this->bestseller->setTaxon($this);
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param mixed $metaTitle
     */
    public function setMetaTitle($metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param mixed $metaKeywords
     */
    public function setMetaKeywords($metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param mixed $metaDescription
     */
    public function setMetaDescription($metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getSeoText()
    {
        return $this->seoText;
    }

    /**
     * @param mixed $seoText
     */
    public function setSeoText($seoText): void
    {
        $this->seoText = $seoText;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getProductRelated()
    {
        return $this->productRelated;
    }

    /**
     * @param mixed $productRelated
     */
    public function setProductRelated($productRelated): void
    {
        $this->productRelated = $productRelated;
    }

    /**
     * @return mixed
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param mixed $teaser
     */
    public function setTeaser($teaser): void
    {
        $this->teaser = $teaser;
    }
}
