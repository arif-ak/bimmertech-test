<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Taxation\Model\TaxCategory as BaseTaxCategory;

/**
 * Class TaxCategory
 * @package AppBundle\Entity
 */
class TaxCategory extends BaseTaxCategory
{
    protected $warehouse;

    /**
     * TaxCategory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->warehouse = new ArrayCollection();
    }

    /**
     * Add warehouse
     *
     * @param Warehouse $warehouse
     *
     */
    public function addWarehouse(Warehouse $warehouse)
    {
        if (true === $this->warehouse->contains($warehouse)) {
            return;
        }
        $this->warehouse[] = $warehouse;
    }

    /**
     * Remove warehouse
     *
     * @param Warehouse $warehouse
     */
    public function removeWarehouse(Warehouse $warehouse)
    {
        if (false === $this->warehouse->contains($warehouse)) {
            return;
        }
        $this->warehouse->removeElement($warehouse);
    }

    /**
     * Get warehouse
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}
