<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

/**
 * Class ShippingMethod
 *
 * @package AppBundle\Entity
 */
class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
{
    /**
     * @var ArrayCollection
     */
    private $warehouse;

    /**
     * ShippingMethod constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->warehouse = new ArrayCollection();
    }

    /**
     * Add warehouse.
     *
     * @param $warehouse
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
     * Remove warehouse.
     *
     * @param Warehouse $warehouse
     *
     */
    public function removeWarehouse(\AppBundle\Entity\Warehouse $warehouse)
    {
        if (false === $this->warehouse->contains($warehouse)) {
            return;
        }
        $this->warehouse->removeElement($warehouse);
    }

    /**
     * Get warehouse.
     *
     * @return Collection
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}
