<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class OrderItemUnitRefund implements ResourceInterface
{
    /** @var int */
    private $id;

    /** @var int */
    private $totalReturnedMoney;

    private $percentOfRefund;

    /** @var \DateTime */
    private $createdAt;

    /** @var OrderRefund */
    private $orderRefund;

    /** @var OrderItemUnit */
    private $orderItemUnit;

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
     * @return int
     */
    public function getTotalReturnedMoney(): ?int
    {
        return $this->totalReturnedMoney;
    }

    /**
     * @param int $totalReturnedMoney
     */
    public function setTotalReturnedMoney(?int $totalReturnedMoney): void
    {
        $this->totalReturnedMoney = $totalReturnedMoney;
    }

    /**
     * @return mixed
     */
    public function getPercentOfRefund()
    {
        return $this->percentOfRefund;
    }

    /**
     * @param  $percentOfRefund
     */
    public function setPercentOfRefund($percentOfRefund): void
    {
        $this->percentOfRefund = $percentOfRefund;
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
     * @return OrderRefund
     */
    public function getOrderRefund(): ?OrderRefund
    {
        return $this->orderRefund;
    }

    /**
     * @param OrderRefund $orderRefund
     */
    public function setOrderRefund(?OrderRefund $orderRefund): void
    {
        $this->orderRefund = $orderRefund;
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
}
