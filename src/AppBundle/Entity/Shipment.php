<?php


namespace AppBundle\Entity;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\Shipment as BaseShipment;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * Class Shipment
 * @package AppBundle\Entity
 */
class Shipment extends BaseShipment implements ShipmentInterface, ImagesAwareInterface
{
    /**
     * @var int
     */
    protected $dhlNumberOfPieces;
    /**
     * @var string
     */
    protected $dhlWeight;
    /**
     * @var string
     */
    protected $dhlInsuredAmount;

    /**
     * @var ShippingExport
     */
    protected $shippingExport;

    /**
     * @var Collection|ImageInterface[]
     */
    protected $images;

    /**
     * @var string
     */
    protected $courier;

    /**
     * @var string
     */
    protected $length;

    /**
     * @var string
     */
    protected $height;

    /**
     * @var string
     */
    protected $width;

    /**
     * Shipment constructor.
     */
    public function __construct($statusReady = false)
    {
        parent::__construct();

        $this->images = new ArrayCollection();
        $this->generateStatus($statusReady);
    }

    /**
     * @param $statusReady
     */
    private function generateStatus($statusReady)
    {
        if ($statusReady) {
            $this->setState(ShipmentInterface::STATE_READY);
        } else {
            $this->setState(ShipmentInterface::STATE_CART);
        }
    }

    /**
     * @return int
     */
    public function getDhlNumberOfPieces(): ?int
    {
        return $this->dhlNumberOfPieces;
    }

    /**
     * @param int $dhlNumberOfPieces
     */
    public function setDhlNumberOfPieces(int $dhlNumberOfPieces)
    {
        $this->dhlNumberOfPieces = $dhlNumberOfPieces;
    }

    /**
     * @return string
     */
    public function getDhlWeight(): ?string
    {
        return $this->dhlWeight;
    }

    /**
     * @param string $dhlWeight
     */
    public function setDhlWeight(string $dhlWeight)
    {
        $this->dhlWeight = $dhlWeight;
    }

    /**
     * @return string
     */
    public function getDhlInsuredAmount(): ?string
    {
        return $this->dhlInsuredAmount;
    }

    /**
     * @param string $dhlInsuredAmount
     */
    public function setDhlInsuredAmount(string $dhlInsuredAmount)
    {
        $this->dhlInsuredAmount = $dhlInsuredAmount;
    }

    /**
     * @return ShippingExport
     */
    public function getShippingExport(): ShippingExport
    {
        return $this->shippingExport;
    }

    /**
     * @param ShippingExport $shippingExport
     */
    public function setShippingExport(ShippingExport $shippingExport)
    {
        $this->shippingExport = $shippingExport;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipMethod()
    {
        /** @var OrderItemUnit $unit */
        $unit = $this->getUnits()->get(0);
        /** @var Warehouse $warehouse */
        $warehouse = $unit->getOrderItem()->getWarehouse();
        $shipMethods = $warehouse->getShippingMethod();

        return $shipMethods;
    }

    /**
     * @param null|ShippingMethodInterface $method
     */
    public function setShipMethod(?ShippingMethodInterface $method): void
    {
        parent::setMethod($method);
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param string $type
     * @return Collection
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ImageInterface $image) use ($type) {
            return $type === $image->getType();
        });
    }

    /**
     * @return bool
     */
    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    /**
     * @param ImageInterface $image
     * @return bool
     */
    public function hasImage(ImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    /**
     * @param ImageInterface $image
     */
    public function addImage(ImageInterface $image): void
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    /**
     * @param ImageInterface $image
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
    public function getCourier(): ?string
    {
        return $this->courier;
    }

    /**
     * @param string $courier
     */
    public function setCourier(?string $courier): void
    {
        $this->courier = $courier;
    }

    /**
     * @return string
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight(?string $height): void
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getLength(): ?string
    {
        return $this->length;
    }

    /**
     * @param string $length
     */
    public function setLength(?string $length): void
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth(?string $width): void
    {
        $this->width = $width;
    }
}
