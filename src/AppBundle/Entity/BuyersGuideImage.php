<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class BuyersGuideImage implements ResourceInterface
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
    }
    public function getId()
    {
        return $this->id;
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
}
