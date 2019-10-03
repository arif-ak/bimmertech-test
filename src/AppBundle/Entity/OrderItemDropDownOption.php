<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class OrderItemDropDownOption
 *
 * @package AppBundle\Entity
 */
class OrderItemDropDownOption implements ResourceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var OrderItem
     */
    protected $orderItem;

    /**
     * @var DropDownOption
     */
    protected $dropDownOption;

    /**
     * @var
     */
    protected $type;

    public function __construct()
    {
        $this->state = OrderInterface::STATUS_NOT_CODED;
    }

    /**
     * @return mixed
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param mixed $orderItem
     */
    public function setOrderItem($orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

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
    public function getDropDownOption(): ?DropDownOption
    {
        return $this->dropDownOption;
    }

    /**
     * @param mixed $dropDownOption
     */
    public function setDropDownOption($dropDownOption): void
    {
        $this->dropDownOption = $dropDownOption;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}