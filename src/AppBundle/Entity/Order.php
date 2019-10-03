<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * Class Order
 * @package AppBundle\Entity
 */
class Order extends BaseOrder implements OrderInterface
{
    /**
     * Order constructor.
     * @param string $source
     */
    public function __construct($source = OrderSourceInerface::SOURCE_WEBSITE)
    {
        parent::__construct();
        $this->source = $source;
        $this->supportStatus = OrderInterface::STATUS_NOT_REQUIRED;
        $this->codingStatus = OrderInterface::STATUS_NOT_REQUIRED;
        $this->usbCodingStatus = OrderInterface::STATUS_NOT_REQUIRED;
        $this->orderRefund = new ArrayCollection();
        $this->orderNotes = new ArrayCollection();
    }

    /**
     * @var string
     */
    protected $vin;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var \AppBundle\Entity\UserSale
     */
    protected $userSale;

    /**
     * @var \AppBundle\Entity\Warehouse
     */
    protected $warehouse;

    /**
     * @var string
     */
    protected $vatNumber;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $provinceName;

    /**
     * @var string
     */
    private $supportStatus;

    /**
     * @var string
     */
    private $usbCodingStatus;

    /**
     * @var string
     */
    private $codingStatus;

    /**
     * @var ArrayCollection
     */
    private $orderNotes;

    /** @var Collection */
    private $orderRefund;

    /**
     * @var string
     */
    private $compatibility;

    /**
     * @var History
     */
    private $history;

    /**
     * Set vin
     *
     * @param string $vin
     *
     * @return Order
     */
    public function setVin($vin)
    {
        $this->vin = $vin;

        return $this;
    }

    /**
     * Get vin
     *
     * @return string
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return Order
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set userSale
     *
     * @param UserSale $userSale
     *
     * @return Order
     */
    public function setUserSale(UserSale $userSale = null)
    {
        $this->userSale = $userSale;

        return $this;
    }

    /**
     * Get userSale
     *
     * @return UserSale
     */
    public function getUserSale()
    {
        return $this->userSale;
    }

    /**
     * Set warehouse
     *
     * @param Warehouse $warehouse
     *
     * @return Order
     */
    public function setWarehouse(Warehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * Set vatNumber
     *
     * @param string $vatNumber
     *
     * @return Order
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * Get vatNumber
     *
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @var boolean
     */
    protected $hasTax;

    /**
     * Set hasTax
     *
     * @param boolean $hasTax
     *
     * @return Order
     */
    public function setHasTax($hasTax)
    {
        $this->hasTax = $hasTax;

        return $this;
    }

    /**
     * Get hasTax
     *
     * @return boolean
     */
    public function getHasTax()
    {
        return $this->hasTax;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return Order
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set provinceName
     *
     * @param string $provinceName
     *
     * @return Order
     */
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;

        return $this;
    }

    /**
     * Get provinceName
     *
     * @return string
     */
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * @return int
     */
    public function getShippingTotal(): int
    {
        $shippingTotal = $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
//        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);

        return $shippingTotal;
    }

    /**
     * @return string
     */
    public function getSupportStatus(): string
    {
        return $this->supportStatus;
    }

    /**
     * @param string $supportStatus
     */
    public function setSupportStatus(string $supportStatus)
    {
        $this->supportStatus = $supportStatus;
    }

    /**
     * @return string
     */
    public function getCodingStatus(): string
    {
        return $this->codingStatus;
    }

    /**
     * @param string $codingStatus
     */
    public function setCodingStatus(string $codingStatus): void
    {
        $this->codingStatus = $codingStatus;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderNotes(): ArrayCollection
    {
        return $this->orderNotes;
    }

    /**
     * @return string
     */
    public function getCompatibility(): ?string
    {
        $statuses = $this->getItems()->map(function ($item) {
            /** @var OrderItem $item */
            return $item->getCompatible();
        });

        foreach ($statuses as $item) {
            if ($item == 'Not verified') {
                return $item;
            }
            if ($item == 'Not compatible') {
                return $item;
            }
            if ($item == 'Compatible') {
                return $item;
            }
        }
        return 'Not verified';
    }

    /**
     * @return Collection
     */
    public function getOrderRefund(): ?Collection
    {
        return $this->orderRefund;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderRefund(OrderRefund $orderRefund): void
    {
        if (true === $this->orderRefund->contains($orderRefund)) {
            return;
        }
        $this->orderRefund->add($orderRefund);
        $orderRefund->setOrder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrderRefund(OrderRefund $orderRefund): void
    {
        if (false === $this->orderRefund->contains($orderRefund)) {
            return;
        }
        $this->orderRefund->removeElement($orderRefund);
    }

    /**
     * @return string
     */
    public function getUsbCodingStatus(): string
    {
        return $this->usbCodingStatus;
    }

    /**
     * @param string $usbCodingStatus
     */
    public function setUsbCodingStatus(string $usbCodingStatus): void
    {
        $this->usbCodingStatus = $usbCodingStatus;
    }
}
