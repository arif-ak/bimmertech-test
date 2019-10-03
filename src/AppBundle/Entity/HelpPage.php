<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class HelpPage
 * @package AppBundle\Entity
 */
class HelpPage implements ResourceInterface
{

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $title;

    /**
     * @var
     */
    private $metaTitle;

    /**
     * @var
     */
    private $metaDescription;

    /**
     * @var
     */
    private $metaKeywords;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
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
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param mixed $metaTitle
     */
    public function setMetaTitle($metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param mixed $metaDescription
     */
    public function setMetaDescription($metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param mixed $metaKeywords
     */
    public function setMetaKeywords($metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }
}