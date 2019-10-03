<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class BuyersGuideProductOption
 * @ORM\Table(name="app_buyers_guide_product_option")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuyersGuideProductOptionRepository")
 */
class BuyersGuideProductOption implements ResourceInterface
{
    /**
     * @var $id
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var object
     */
    protected $owner;

    protected $enable;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var BuyersGuideOption
     */
    protected $option;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @return mixed
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * BuyersGuideProductOption constructor.
     */
    public function __construct()
    {
//        $this->images = new ArrayCollection();
    }
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     *
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param BuyersGuideOption $option
     */
    public function setOption(BuyersGuideOption $option): void
    {
        $this->option = $option;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(?\SplFileInfo $file): void
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * @param mixed $enable
     */
    public function setEnable($enable): void
    {
        $this->enable = $enable;
    }
}
