<?php


namespace AppBundle\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class MediaFolder implements ResourceInterface, MediaFolderInterface, ImagesAwareInterface
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $imagesCount = 0;

    /**
     * @var integer
     */
    protected $foldersCount = 0;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @var MediaFolder
     */
    protected $parent;

    /**
     * @var ArrayCollection;
     */
    protected $children;

    /**
     * @var ArrayCollection;
     */
    protected $images;

    /**
     * MediaFolder constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->children = new  ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getImagesCount(): int
    {
        return $this->images->count();
    }

    /**
     *
     */
    public function setImagesCount(): void
    {
        $this->imagesCount = $this->images->count();
    }

    /**
     * @return int
     */
    public function getFoldersCount(): int
    {
        return $this->foldersCount = $this->children->count();
    }

    /**
     *
     */
    public function setFoldersCount(): void
    {
        $this->foldersCount = $this->children->count();
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return MediaFolder
     */
    public function getParent(): ?MediaFolder
    {
        return $this->parent;
    }

    /**
     * @param MediaFolder $parent
     */
    public function setParent(?MediaFolder $parent): void
    {
        $this->parent = $parent;
        if (null !== $parent) {
            $parent->addChild($this);
            $parent->setFoldersCount();
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(MediaFolderInterface $mediaFolder): bool
    {
        return $this->children->contains($mediaFolder);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(MediaFolderInterface $mediaFolder): void
    {
        if (!$this->hasChild($mediaFolder)) {
            $this->children->add($mediaFolder);
        }

        if ($this !== $mediaFolder->getParent()) {
            $mediaFolder->setParent($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(MediaFolderInterface $mediaFolder): void
    {
        if ($this->hasChild($mediaFolder)) {
            $mediaFolder->setParent(null);

            $this->children->removeElement($mediaFolder);
        }
    }

    /**
     * @return
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
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
     * {@inheritdoc}
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ImageInterface $image) use ($type) {
            return $type === $image->getType();
        });
    }
}