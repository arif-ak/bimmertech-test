<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\PaymentMethod as BasePaymentMethod;

//use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;

/**X
 * Class PaymentMethod
 * @package AppBundle\Entity
 */
class PaymentMethod extends BasePaymentMethod
{
    protected $warehouse;

    /**
     * PaymentMethod constructor.
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
        $warehouse->addPaymentMethod($this);
//        return $this;
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

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }
}
