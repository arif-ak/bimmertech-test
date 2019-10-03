<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

class OrderRefund implements ResourceInterface
{
    /** @var int */
    private $id;

    private $totalReturnedMoney;

    /** @var int */
    private $percentOfRefund;

    /** @var \DateTime */
    private $createdAt;

    /** @var ArrayCollection */
    private $orderItemUnitRefund;

    /** @var OrderItemUnitReturn */
    private $orderItemUnitReturn;

    /** @var Order */
    protected $order;

    /** @var string */
    protected $comment;

    public function __construct()
    {
        $this->orderItemUnitRefund = new ArrayCollection();
    }

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
     * @param $totalReturnedMoney
     */
    public function setTotalReturnedMoney($totalReturnedMoney): void
    {
        $this->totalReturnedMoney = $totalReturnedMoney;
    }

    /**
     * @return int
     */
    public function getPercentOfRefund()
    {
        return $this->percentOfRefund;
    }

    /**
     * @param int $percentOfRefund
     */
    public function setPercentOfRefund(?int $percentOfRefund): void
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
     * @return ArrayCollection
     */
    public function getOrderItemUnitRefund(): ?ArrayCollection
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
        $orderItemUnitRefund->setOrderRefund($this);
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

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return OrderItemUnitReturn
     */
    public function getOrderItemUnitReturn(): OrderItemUnitReturn
    {
        return $this->orderItemUnitReturn;
    }

    /**
     * @param OrderItemUnitReturn $orderItemUnitReturn
     */
    public function setOrderItemUnitReturn(OrderItemUnitReturn $orderItemUnitReturn): void
    {
        $this->orderItemUnitReturn = $orderItemUnitReturn;
    }
}
