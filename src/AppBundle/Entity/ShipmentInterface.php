<?php


namespace AppBundle\Entity;

use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

/**
 * Interface ShipmentInterface
 * @package AppBundle\Entity
 */
interface ShipmentInterface extends BaseShipmentInterface
{
    const STATE_DELIVERED = 'delivered';
    const STATE_NOT_SHIPPED = "not shipped";
    const STATE_PARTIALLY_SHIPPED = "partially shipped";
    const STATE_BACK_ORDER = "backorder";
    const STATE_EXPIRED = "expired";
    const GENERAL_METHOD = "other";

    /**
     * @return int|null
     */
    public function getDhlNumberOfPieces(): ?int;

    /**
     * @param int $dhlNumberOfPieces
     */
    public function setDhlNumberOfPieces(int $dhlNumberOfPieces);

    /**
     * @return string
     */
    public function getDhlWeight(): ?string;

    /**
     * @param string $dhlWeight
     */
    public function setDhlWeight(string $dhlWeight);

    /**
     * @return string
     */
    public function getDhlInsuredAmount(): ?string;

    /**
     * @param string $dhlInsuredAmount
     */
    public function setDhlInsuredAmount(string $dhlInsuredAmount);

}