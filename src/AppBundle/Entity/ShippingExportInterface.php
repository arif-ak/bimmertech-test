<?php
/**
 * Created by PhpStorm.
 * User: Taras Terletskij
 * Date: 16.05.2018
 * Time: 13:10
 */

namespace AppBundle\Entity;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface as BaseShippingExportInterface;
use Sylius\Component\Core\Model\ShipmentInterface as BaseShipmentInterface;

/**
 * Interface ShippingExportInterface
 * @package AppBundle\Entity
 */
interface ShippingExportInterface extends BaseShippingExportInterface
{
    /**
     * @return ShipmentInterface
     */
    public function getShipment(): ?BaseShipmentInterface;

    /**
     * @param ShipmentInterface $shipment
     */
    public function setShipment(?BaseShipmentInterface $shipment): void;
}