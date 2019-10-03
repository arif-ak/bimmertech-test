<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class DropDownOption
 *
 * @package AppBundle\Entity
 */
class DropDownOption implements ResourceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $price;

    /**
     * @var integer
     */
    protected $position=0;

    /**
     * @var DropDown;
     */
    protected $dropDown;

    /**
     * @var ArrayCollection
     */
    protected $orderItems;

    /**
     * @var OrderItemDropDownOption
     */
    protected $orderItemDropDownOptions;

    /**
     * @var boolean
     */
    protected $isNone;

    /**
     * DropDownOption constructor.
     */
    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return DropDown
     */
    public function getDropDown(): ?DropDown
    {
        return $this->dropDown;
    }

    /**
     * @param DropDown $dropDown
     */
    public function setDropDown(DropDown $dropDown): void
    {
        $this->dropDown = $dropDown;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return bool
     */
    public function getIsNone()
    {

        return  'none' == strtolower($this->name);
    }
}
