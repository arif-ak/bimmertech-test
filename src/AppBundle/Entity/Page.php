<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Stripe\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class Page
 *
 * @package AppBundle\Entity
 */
class Page implements ResourceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var  Collection
     */
    protected $pageDescriptions;

    /**
     * @var string
     */
    protected $metaTitle;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var string
     */
    protected $metaKeywords;

    /**
     * @var string
     */
    protected $content;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->pageDescriptions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getPageDescriptions()
    {
        return $this->pageDescriptions;
    }

    /**
     * @param PageDescription $pageDescription
     */
    public function addPageDescription(PageDescription $pageDescription)
    {
        if (true === $this->pageDescriptions->contains($pageDescription)) {
            return;
        }
        $this->pageDescriptions->add($pageDescription);
        $pageDescription->setPage($this);

    }

    /**
     * @param PageDescription $pageDescription
     */
    public function removePageDescription(PageDescription $pageDescription)
    {
        if (false === $this->pageDescriptions->contains($pageDescription)) {
            return;
        }
        $this->pageDescriptions->removeElement($pageDescription);
    }

    /**
     * @return string
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

}