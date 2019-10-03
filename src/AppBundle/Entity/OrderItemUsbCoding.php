<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class OrderItemUsbCoding implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $isCoded;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var OrderItem
     */
    protected $orderItem;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isCoded(): ?bool
    {
        return $this->isCoded;
    }

    /**
     * @param bool $isCoded
     */
    public function setIsCoded(?bool $isCoded): void
    {
        $this->isCoded = $isCoded;
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
     * @return OrderItem
     */
    public function getOrderItem(): ?OrderItem
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     */
    public function setOrderItem(?OrderItem $orderItem): void
    {
        $this->orderItem = $orderItem;
    }
}
