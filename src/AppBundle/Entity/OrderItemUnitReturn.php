<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class OrderItemUnitReturn implements ResourceInterface
{
    /** @var int */
    private $id;

    /** @var \DateTime */
    private $createdAt;

    /** @var OrderItemUnit */
    private $orderItemUnit;

    /** @var OrderRefund */
    private $orderRefund;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return OrderItemUnit
     */
    public function getOrderItemUnit(): ?OrderItemUnit
    {
        return $this->orderItemUnit;
    }

    /**
     * @param OrderItemUnit $orderItemUnit
     */
    public function setOrderItemUnit(?OrderItemUnit $orderItemUnit): void
    {
        $this->orderItemUnit = $orderItemUnit;
    }

    /**
     * @return OrderRefund
     */
    public function getOrderRefund(): OrderRefund
    {
        return $this->orderRefund;
    }

    /**
     * @param OrderRefund $orderRefund
     */
    public function setOrderRefund(OrderRefund $orderRefund): void
    {
        $this->orderRefund = $orderRefund;
    }
}
