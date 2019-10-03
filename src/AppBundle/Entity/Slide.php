<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class Slider
 *
 * @ORM\Table(name="slide")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlideRepository")
 */
class Slide implements ResourceInterface, ImagesAwareInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string;
     */
    protected $name;

    /**
     * @var string;
     */
    protected $description;

    /**
     * @var string;
     */
    protected $url;

    /**
     * @var Collection|ImageInterface[]
     */
    protected $images;

    /**
     * @var int;
     */
    protected $position = 0;

    /**
     * @var boolean
     */
    protected $enabled = false;

    /**
     * @var Slider
     */
    protected $slider;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var Product
     */
    protected $product;


    /**
     * Slide constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->addImage(new SlideImage());
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url = null): void
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getPosition(): ?int
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
     * @return mixed
     */
    public function getSlider()
    {
        return $this->slider;
    }

    /**
     * @param mixed $slider
     */
    public function setSlider($slider): void
    {
        $this->slider = $slider;
    }

    /**
     * @return ArrayCollection|Collection|ImageInterface[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ImageInterface $image) use ($type) {
            return $type === $image->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ImageInterface $image): void
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price = null): void
    {
        $this->price = $price;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product = null): void
    {
        $this->product = $product;
    }
}
