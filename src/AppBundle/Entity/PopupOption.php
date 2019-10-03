<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class FAQQuestion
 * @ORM\Table(name="app_popup_option")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PopupOptionRepository")
 */
class PopupOption implements PopupOptionInterface, ResourceInterface
{
    const NPT = 123;
    const PDC = 124;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var
     */
    protected $compatibility;

    protected $vinCheckServiceId;

    /**
     * @var string|null
     */
    protected $title;

    protected $description;

    public function __construct()
    {
    }

    /**
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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
    public function getVinCheckServiceId()
    {
        return $this->vinCheckServiceId;
    }

    /**
     * @param mixed $vinCheckServiceId
     */
    public function setVinCheckServiceId($vinCheckServiceId): void
    {
        $this->vinCheckServiceId = $vinCheckServiceId;
    }
}
