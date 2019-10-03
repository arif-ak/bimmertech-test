<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * Class Product
 * @package AppBundle\Entity
 */
class Product extends BaseProduct implements ProductInterface
{
    const ADDON_CAHANNEL = 'addons.bimmer-tech.net';
    const INCLUDED = 'includedAddon';

    protected $id;

    /**
     * @var
     */
    protected $compatibility;

    /**
     * @var bool
     */
    protected $recomended = false;

    /**
     * @var ArrayCollection
     */
    protected $properties;

    /**
     * @var ArrayCollection
     */
    protected $productCarModel;

    /**
     * @var ArrayCollection
     */
    protected $buyersOption;

    /**
     * @var ArrayCollection
     */
    protected $buyersRelated;

    /**
     * @var ArrayCollection
     */
    protected $buyersImage;

    /**
     * @var string $buyersHeaderContent
     */
    protected $buyersHeaderContent;

    /**
     * @var string $buyersFooterContent
     */
    protected $buyersFooterContent;

    /**
     * @var
     */
    protected $taxonDescription;

    /**
     * @var
     */
    protected $interestingProducts;

    /**
     * @var string $addonInformation
     */
    protected $addonInformation;

    /**
     * @var DropDown
     */
    protected $productDropDowns;

    /**
     * @var ArrayCollection
     */
    protected $productPopupOption;

    /**
     * @var SavePrice
     */
    protected $savePrice;

    /**
     * @var ArrayCollection
     */
    protected $productDescriptions;

    /**
     * @var ArrayCollection
     */
    protected $productInstallers;

    /***
     * @var string
     */
    protected $type;

    /**
     * @var ArrayCollection
     */
    protected $blogPostProduct;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $installer;

    /**
     * @var
     */
    protected $slides;

    /**
     * @var Color
     */
    protected $flagColor;

    /**
     * @var string
     */
    protected $flagName;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->buyersImage = new ArrayCollection();
        $this->addBuyersImage(new BuyersGuideImage());
        $this->buyersOption = new ArrayCollection();
        $this->buyersRelated = new ArrayCollection();
        $this->productPopupOption = new ArrayCollection();
        $this->productDescriptions = new ArrayCollection();
        $this->productInstallers = new ArrayCollection();
        $this->productCarModel = new ArrayCollection();
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
    public function getRecomended()
    {
        return $this->recomended;
    }

    /**
     * @param mixed $recomended
     */
    public function setRecomended($recomended)
    {
        $this->recomended = $recomended;
    }

    /**
     * @return string
     */
    public function getCompatibility()
    {
        return $this->compatibility;
    }

    /**
     * @return string
     */
    public function getAddonInformation(): ?string
    {
        return $this->addonInformation;
    }

    /**
     * @param string $addonInformation
     */
    public function setAddonInformation(?string $addonInformation): void
    {
        $this->addonInformation = $addonInformation;
    }

    /**
     * @param string $compatibility
     */
    public function setCompatibility($compatibility)
    {
        $this->compatibility = $compatibility;
    }

    public function getTranslation(?string $locale = 'en_US'): TranslationInterface
    {
        return parent::getTranslation($locale);
    }

    /**
     * @return int|null
     */
    public function getVinCheckProductId(): ?int
    {
        /** @var ProductVariant $productVariant */
        $productVariant = $this->getVariants()->get(0);
        return $productVariant->getVincheckserviceId();
    }

    /**
     * @return mixed
     */
    public function getTaxonDescription()
    {
        return $this->taxonDescription;
    }

    /**
     * @param mixed $taxonDescription
     */
    public function setTaxonDescription($taxonDescription): void
    {
        $this->taxonDescription = $taxonDescription;
    }

    /**
     * @return ArrayCollection
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return ArrayCollection
     */
    public function getBuyersOption()
    {
        return $this->buyersOption;
    }

    /**
     * @return bool
     */
    public function hasBuyersOption(): bool
    {
        return !$this->buyersOption->isEmpty();
    }

    /**
     * @param BuyersGuideProductOption $buyersOption
     */
    public function addBuyersOption($buyersOption)
    {
        if ($buyersOption->getOption()) {
            if ('No option' === $buyersOption->getOption()->getName()) {
                $this->removeBuyersOption($buyersOption);
                return;
            }
        }

        if (true === $this->buyersOption->contains($buyersOption)) {
            return;
        }
        $this->buyersOption->add($buyersOption);
        $buyersOption->setProduct($this);
        $buyersOption->setOwner($this);
    }

    /**
     * @param BuyersGuideProductOption $buyersOption
     */
    public function removeBuyersOption(BuyersGuideProductOption $buyersOption)
    {
        if (false === $this->buyersOption->contains($buyersOption)) {
            return;
        }

        $this->buyersOption->removeElement($buyersOption);
        $buyersOption->setProduct(null);
        $buyersOption->setOwner(null);
    }

    /**
     * @return mixed
     */
    public function getBlogPostProduct()
    {
        return $this->blogPostProduct;
    }

    /**
     * @return bool
     */
    public function hasBlogPostProduct(): bool
    {
        return !$this->blogPostProduct->isEmpty();
    }

    /**
     * @param BlogPostProduct $blogPostProduct
     */
    public function addBlogPostProduct(BlogPostProduct $blogPostProduct)
    {
        if (true === $this->blogPostProduct->contains($blogPostProduct)) {
            return;
        }

        $this->blogPostProduct->add($blogPostProduct);
        $blogPostProduct->setProduct($this);
    }

    /**
     * @param blogPostProduct $blogPostProduct
     */
    public function removeBlogPostProduct(BlogPostProduct $blogPostProduct)
    {
        if (false === $this->blogPostProduct->contains($blogPostProduct)) {
            return;
        }

        $this->blogPostProduct->removeElement($blogPostProduct);
        $blogPostProduct->setProduct(null);
    }


    /**
     * @return ArrayCollection
     */
    public function getBuyersRelated()
    {
        return $this->buyersRelated;
    }

    /**
     * @param BuyersGuideProductRelated $buyersRelated
     */
    public function addBuyersRelated($buyersRelated)
    {
        if (!$buyersRelated->getRelatedProduct()) {
            return;
        }

        if ($buyersRelated) {
            if (true === $this->buyersRelated->contains($buyersRelated)) {
                return;
            }

            $this->buyersRelated->add($buyersRelated);
            $buyersRelated->setCurrentProduct($this);
        }
    }


    /**
     * @param BuyersGuideProductRelated $buyersRelated
     */
    public function removeBuyersRelated(BuyersGuideProductRelated $buyersRelated)
    {
        if (false === $this->buyersRelated->contains($buyersRelated)) {
            return;
        }

        $this->buyersRelated->removeElement($buyersRelated);
        $buyersRelated->setCurrentProduct(null);
    }

    /**
     * @return mixed
     */
    public function getInterestingProducts()
    {
        return $this->interestingProducts;
    }

    /**
     * @param mixed $interestingProducts
     */
    public function setInterestingProducts($interestingProducts): void
    {
        $this->interestingProducts = $interestingProducts;
    }

    /**
     * @return ArrayCollection
     */
    public function getBuyersImage()
    {
        return $this->buyersImage;
    }

    /**
     * @return bool
     */
    public function hasBuyersImage(): bool
    {
        return !$this->buyersImage->isEmpty();
    }

    /**
     * @param BuyersGuideImage $buyersImage
     */
    public function addBuyersImage(BuyersGuideImage $buyersImage)
    {
        if (true === $this->buyersImage->contains($buyersImage)) {
            return;
        }
        $this->buyersImage->add($buyersImage);
        $buyersImage->setProduct($this);
        $buyersImage->setOwner($this);
    }

    /**
     * @param BuyersGuideImage $buyersOption
     */
    public function removeBuyersImage(BuyersGuideImage $buyersImage)
    {
        if (false === $this->buyersImage->contains($buyersImage)) {
            return;
        }

        $this->buyersImage->removeElement($buyersImage);
        $buyersImage->setProduct(null);
        $buyersImage->setOwner(null);
    }

    /**
     * @return string
     */
    public function getBuyersHeaderContent(): ?string
    {
        return $this->buyersHeaderContent;
    }

    /**
     * @param string $buyersHeaderContent
     */
    public function setBuyersHeaderContent($buyersHeaderContent): void
    {
        $this->buyersHeaderContent = $buyersHeaderContent;
    }

    /**
     * @return string
     */
    public function getBuyersFooterContent(): ?string
    {
        return $this->buyersFooterContent;
    }

    /**
     * @param string $buyersFooterContent
     */
    public function setBuyersFooterContent($buyersFooterContent): void
    {
        $this->buyersFooterContent = $buyersFooterContent;
    }

    /**
     * @return mixed
     */
    public function getProductDropDowns()
    {
        return $this->productDropDowns;
    }

    /**
     * @param mixed $productDropDowns
     */
    public function setProductDropDowns($productDropDowns): void
    {
        $this->productDropDowns = $productDropDowns;
    }

    /**
     * @return mixed
     */
    public function getProductPopupOption()
    {
        return $this->productPopupOption;
    }

    /**
     * @param mixed $productPopupOption
     */
    public function setProductPopupOption($productPopupOption): void
    {
        $this->productPopupOption = $productPopupOption;
    }

    /**
     * @return SavePrice
     */
    public function getSavePrice(): ?SavePrice
    {
        return $this->savePrice;
    }

    /**
     * @param SavePrice $savePrice
     */
    public function setSavePrice(SavePrice $savePrice): void
    {
        $this->savePrice = $savePrice;
        $savePrice->setProduct($this);
    }


    /**
     * @return mixed
     */
    public function getProductDescriptions()
    {
        return $this->productDescriptions;
    }

    /**
     * @param ProductDescription $productDescription
     */
    public function addProductDescription(ProductDescription $productDescription)
    {
        if (true === $this->productDescriptions->contains($productDescription)) {
            return;
        }
        $this->productDescriptions->add($productDescription);
        $productDescription->setProduct($this);

    }

    /**
     * @param ProductDescription $productDescription
     */
    public function removeProductDescription(ProductDescription $productDescription)
    {
        if (false === $this->productDescriptions->contains($productDescription)) {
            return;
        }
        $this->productDescriptions->removeElement($productDescription);
    }

    /**
     * @return mixed
     */
    public function getProductInstallers()
    {
        return $this->productInstallers;
    }

    /**
     * @param ProductInstaller $productInstaller
     */
    public function addProductInstaller(ProductInstaller $productInstaller)
    {
        if (true === $this->productInstallers->contains($productInstaller)) {
            return;
        }
        $this->productInstallers->add($productInstaller);
        $productInstaller->setProduct($this);

    }

    /**
     * @param ProductInstaller $productInstaller
     */
    public function removeProductInstaller(ProductInstaller $productInstaller)
    {
        if (false === $this->productInstallers->contains($productInstaller)) {
            return;
        }
        $this->productInstallers->removeElement($productInstaller);
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getInstaller(): ?string
    {
        return $this->installer;
    }

    /**
     * @param string $installer
     */
    public function setInstaller(?string $installer): void
    {
        $this->installer = $installer;
    }

    public function getProductCarModel()
    {
        return $this->productCarModel;
    }

    /**
     * @return bool
     */
    public function hasProductCarModel(): bool
    {
        return !$this->productCarModel->isEmpty();
    }

    /**
     * @param ProductCarModel $productCarModel
     */
    public function addProductCarModel(ProductCarModel $productCarModel)
    {
        if (true === $this->productCarModel->contains($productCarModel)) {
            return;
        }

        $this->productCarModel->add($productCarModel);
        $productCarModel->setProduct($this);
    }

    /**
     * @param ProductCarModel $carModel
     */
    public function removeProductCarModel(ProductCarModel $productCarModel)
    {
        if (false === $this->productCarModel->contains($productCarModel)) {
            return;
        }

        $this->productCarModel->removeElement($productCarModel);
        $productCarModel->setProduct(null);
    }

    /**
     * @return Color
     */
    public function getFlagColor(): ?Color
    {
        return $this->flagColor;
    }

    /**
     * @param Color $flagColor
     */
    public function setFlagColor(Color $flagColor): void
    {
        $this->flagColor = $flagColor;
    }

    /**
     * @return string
     */
    public function getFlagName(): ?string
    {
        return $this->flagName;
    }

    /**
     * @param string $flagName
     */
    public function setFlagName(?string $flagName): void
    {
        $this->flagName = $flagName;
    }
}
