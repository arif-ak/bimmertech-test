<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnit as BaseOrderItemUnit;

/**
 * Class OrderItemUnit
 * @package AppBundle\Entity
 */
class OrderItemUnit extends BaseOrderItemUnit
{
    protected $warehouse;

    /** @var OrderItemUnitReturn */
    protected $orderItemUnitReturn;

    /** @var ArrayCollection */
    protected $orderItemUnitRefund;

    public function __construct(OrderItemInterface $orderItem)
    {
        parent::__construct($orderItem);
        $this->orderItemUnitRefund = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @return OrderItemUnitReturn
     */
    public function getOrderItemReturn(): ?OrderItemUnitReturn
    {
        return $this->orderItemUnitReturn;
    }

    /**
     * @param OrderItemUnitReturn $orderItemUnitReturn
     */
    public function setOrderItemReturn(?OrderItemUnitReturn $orderItemUnitReturn): void
    {
        $this->orderItemUnitReturn = $orderItemUnitReturn;
    }

    /**
     * @return Collection
     */
    public function getOrderItemUnitRefund(): ?Collection
    {
        return $this->orderItemUnitRefund;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderItemUnitRefund(OrderItemUnitRefund $orderItemUnitRefund): void
    {
        if (true === $this->orderItemUnitRefund->contains($orderItemUnitRefund)) {
            return;
        }
        $this->orderItemUnitRefund->add($orderItemUnitRefund);
        $orderItemUnitRefund->setOrderItemUnit($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrderItemUnitRefund(OrderItemUnitRefund $orderItemUnitRefund): void
    {
        if (false === $this->orderItemUnitRefund->contains($orderItemUnitRefund)) {
            return;
        }
        $this->orderItemUnitRefund->removeElement($orderItemUnitRefund);
    }
}
