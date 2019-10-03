<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class BlogPostImage implements ResourceInterface
{
    const ASPECT_RATIO_ONE_TO_ONE = '1:1';
    const ASPECT_RATIO_TWO_TO_ONE = '2:1';

    /**
     * @var $id
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $aspectRatio;

    /**
     * @var object
     */
    protected $owner;

    /**
     * @var BlogPost
     */
    protected $blogPost;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $isUpdatedFile;

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
     * @return string
     */
    public function getAspectRatio(): ?string
    {
        return $this->aspectRatio;
    }

    /**
     * @param string $aspectRatio
     */
    public function setAspectRatio($aspectRatio): void
    {
        $this->aspectRatio = $aspectRatio;
    }

    /**
     * @return BlogPost
     */
    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    /**
     * @param BlogPost $blogPost
     */
    public function setBlogPost($blogPost): void
    {
        $this->blogPost = $blogPost;
    }

    /**
     * @return string
     */
    public function getIsUpdatedFile(): ?string
    {
        return $this->isUpdatedFile;
    }

    /**
     * @param string $isUpdatedFile
     */
    public function setIsUpdatedFile($isUpdatedFile): void
    {
        $this->isUpdatedFile = $isUpdatedFile;
    }
}
