<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Image;

/**
 * Class MediaImage
 *
 * @package AppBundle\Entity
 */
class MediaImage extends Image
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected  $alt;

    /**
     * MediaImage constructor.
     */
    public function __construct()
    {
        $this->createdAt= new DateTime('now');
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
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt(?string $alt): void
    {
        $this->alt = $alt;
    }
}