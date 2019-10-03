<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class FAQQuestion
 * @ORM\Table(name="app_contact_title")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactTitleRepository")
 */
class ContactTitle implements ResourceInterface
{
    const SUPPORT = "support";
    const SALES = "sales";
    const NONE = "none";

    protected $id;

    protected $name;

    protected $popup;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPopup()
    {
        return $this->popup;
    }

    /**
     * @param mixed $popup
     */
    public function setPopup($popup): void
    {
        $this->popup = $popup;
    }
}
