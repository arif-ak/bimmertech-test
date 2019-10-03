<?php

namespace AppBundle\Entity;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;

/**
 * Class ShippingExport
 * @package AppBundle\Entity
 */
class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
}
