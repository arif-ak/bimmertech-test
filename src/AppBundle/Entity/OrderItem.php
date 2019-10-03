<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * Class OrderItem
 * @package AppBundle\Entity
 */
class OrderItem extends BaseOrderItem implements OrderItemInterface, ImagesAwareInterface
{
    /**
     * @var Warehouse
     */
    protected $warehouse;

    /**
     * @var string
     */
    protected $instruction;

    /**
     * @var string
     */
    protected $supportStatus;

    /**
     * @var \DateTime
     */
    protected $supportDate;

    /**
     * @var string
     */
    protected $codingStatus;

    /**
     * @var \DateTime
     */
    protected $codingDate;

    /**
     * @var Collection|ImageInterface[]
     */
    protected $images;

    /**
     * @var OrderItem
     */
    protected $parent;

    /**
     * @var ArrayCollection;
     */
    protected $addons;

    /**
     * @var ArrayCollection
     */
    protected $orderItemDropDownOptions;

    /**
     * @var SavePrice
     */
    protected $savePrice;

    /**
     * @var string
     */
    protected $type;

    /**\
     * @var int
     */
    protected $price = 0;

    /**
     * @var OrderItemUsbCoding
     */
    protected $orderItemUsbCoding;
    
    /**
     * @var string
     */
    protected $compatible;

    /**
     * OrderItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->supportStatus = OrderItemInterface::NOT_REQUIRED;
        $this->codingStatus = OrderItemInterface::NOT_REQUIRED;
        $this->images = new ArrayCollection();
        $this->addons = new ArrayCollection();
        $this->orderItemDropDownOptions = new ArrayCollection();
    }

    /**
     * Set warehouse
     *
     * @param Warehouse $warehouse
     *
     * @return OrderItem
     */
    public function setWarehouse(Warehouse $warehouse = null): ?OrderItem
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return Warehouse
     */
    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    /**
     * @return string
     */
    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    /**
     * @param string $instruction
     * @return string
     */
    public function setInstruction(?string $instruction): ?string
    {
        if ($instruction) {
            $this->supportStatus = OrderItemInterface::COMPLETE;
        }

        return $this->instruction = $instruction;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ImageInterface $image) use ($type) {
            return $type === $image->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ImageInterface $image): void
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
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
     * @param null|ProductVariantInterface $variant
     */
    public function setVariant(?ProductVariantInterface $variant): void
    {
        parent::setVariant($variant);
        $this->generateStatus();
    }

    /**
     * @return string
     */
    public function getCodingStatus(): ?string
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
     * Generate orderItem statuses
     */
    private function generateStatus()
    {
        /** @var ProductVariant $prodVariant */
        $prodVariant = $this->getVariant();
        if ($prodVariant->getInstruction() !== null) {
            $this->instruction = $prodVariant->getInstruction();
            $this->supportStatus = OrderItemInterface::COMPLETE;
        }
        if ($prodVariant->getInstructionRequired()) {
            $this->supportStatus = OrderItemInterface::NEW;
        }

        if ($prodVariant->getHasHardware() or $prodVariant->getHasSoftware()) {
            $this->codingStatus = OrderItemInterface::NOT_CODED;
        }
    }

    /**
     * @return mixed
     */
    public function getAddons()
    {
        return $this->addons;
    }

    /**
     * @return OrderItem
     */
    public function getParent(): ?OrderItem
    {
        return $this->parent;
    }

    /**
     * @param OrderItem $parent
     */
    public function setParent(OrderItem $parent): void
    {
        $this->parent = $parent;
        if (null !== $parent) {
            $parent->addAddon($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddon(OrderItem $orderItem): bool
    {
        return $this->addons->contains($orderItem);
    }

    /**
     * {@inheritdoc}
     */
    public function addAddon(OrderItem $orderItem): void
    {
        if (!$this->hasAddon($orderItem)) {
            $this->addons->add($orderItem);
        }

        if ($this !== $orderItem->getParent()) {
            $orderItem->setParent($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddon(OrderItem $orderItem): void
    {
        if ($this->hasAddon($orderItem)) {
            $this->addons->removeElement($orderItem);
        }
    }

    /**
     * @return SavePrice
     */
    public function getSavePrice()
    {
        return $this->savePrice;
    }

    /**
     * @param SavePrice $savePrice
     */
    public function setSavePrice(SavePrice $savePrice)
    {
        $this->savePrice = $savePrice;
    }

    /**
     * @param string $type
     * @return OrderItem
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getWarranty()
    {
        $warranty = $this->getAddons()->filter(function ($addon) {
            /** @var OrderItem $addon */
            if ($addon->getType() == 'warranty') {
                return $addon;
            }
        })->first();

        return $warranty ?: null;
    }

    /**
     * @param ProductVariant $productVariant
     */
    public function updateWarranty(ProductVariant $productVariant, $price)
    {
        /** @var OrderItem $warranty */
        $warranty = $this->getWarranty();
        $warranty->setVariant($productVariant);
        $warranty->setPrice($price);
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderItemDropDownOptions()
    {
        return $this->orderItemDropDownOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderItemDropDownOption(OrderItemDropDownOption $orderItemDropDownOption): void
    {
        if (true === $this->orderItemDropDownOptions->contains($orderItemDropDownOption)) {
            return;
        }
        $this->orderItemDropDownOptions->add($orderItemDropDownOption);
        $orderItemDropDownOption->setOrderItem($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrderItemDropDownOption(OrderItemDropDownOption $orderItemDropDownOption): void
    {
        if (false === $this->orderItemDropDownOptions->contains($orderItemDropDownOption)) {
            return;
        }
        $this->orderItemDropDownOptions->removeElement($orderItemDropDownOption);
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $this->getPrice();
        $this->recalculateUnitsTotal();
    }

    /**
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;


        if ($this->getType() == 'includedAddon') {
            $this->price = 0;
        }

        if ($this->getType() == 'warranty' && $price == 1) {
            $this->price = 0;
        }

        $this->setUnitPrice($this->price);
    }

    /**
     * @return OrderItemUsbCoding
     */
    public function getOrderItemUsbCoding(): ?OrderItemUsbCoding
    {
        return $this->orderItemUsbCoding;
    }

    /**
     * @param OrderItemUsbCoding $orderItemUsbCoding
     */
    public function setOrderItemUsbCoding(?OrderItemUsbCoding $orderItemUsbCoding): void
    {
        $this->orderItemUsbCoding = $orderItemUsbCoding;
    }

    /**
     * @return \DateTime
     */
    public function getCodingDate(): ?\DateTime
    {
        return $this->codingDate;
    }

    /**
     * @param \DateTime $codingDate
     */
    public function setCodingDate(?\DateTime $codingDate): void
    {
        $this->codingDate = $codingDate;
    }

    /**
     * @return \DateTime
     */
    public function getSupportDate(): ?\DateTime
    {
        return $this->supportDate;
    }

    /**
     * @param \DateTime $supportDate
     */
    public function setSupportDate(?\DateTime $supportDate): void
    {
        $this->supportDate = $supportDate;
    }
    /**
     * @return string
     */
    public function getCompatible(): ?string
    {
        return $this->compatible;
    }

    /**
     * @param string $compatible
     */
    public function setCompatible(?string $compatible): void
    {
        $this->compatible = $compatible;
    }

}
