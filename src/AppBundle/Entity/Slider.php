<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class Slider
 *
 * @ORM\Table(name="slider")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SliderRepository")
 */
class Slider implements ResourceInterface
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
     * @var boolean
     */
    protected $enabled = false;

    /**
     * @var
     */
    protected $slides;

    /**
     * Slider constructor.
     */
    public function __construct()
    {
        $this->slides = new ArrayCollection();
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
    public function setName(string $name): void
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
     * @return mixed
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * Add order
     *
     * @param Slide $slide
     * @return Slider
     */
    public function addSlides(Slide $slide)
    {
        $this->slides[] = $slide;

        return $this;
    }

    /**
     * Remove order
     *
     * @param Slide $slide
     */
    public function removeSlises(Slide $slide)
    {
        $this->slides->removeElement($slide);
    }

}