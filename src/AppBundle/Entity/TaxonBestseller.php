<?php

namespace AppBundle\Entity;

/**
 * Class TaxonBestseller
 * @package AppBundle\Entity
 */
class TaxonBestseller
{

    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var string;
     */
    protected $name;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var Taxon
     */
    protected $taxon;

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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color): void
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * @param mixed $taxon
     */
    public function seTtaxon($taxon): void
    {
        $this->taxon = $taxon;
    }
}