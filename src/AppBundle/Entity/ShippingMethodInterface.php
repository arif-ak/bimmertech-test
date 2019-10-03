<?php

namespace AppBundle\Entity;

interface ShippingMethodInterface
{
    public const DHL_PL = "dhl_pl";
    public const DHL_AE = "dhl_ae";
    public const DHL = "dhl";
    public const USPS = "usps";
    public const EMS = "ems";
    public const AFTER_SHIP_DHL = 'dhl';
    public const SHIPMENT_NOT_EXIST = 4004;
}
