<?php

namespace AppBundle\EventListener;

use AppBundle\Service\USPSGateway;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;

/**
 * Class AppUSPSExportEventListener
 * @package AppBundle\EventListener
 */
final class AppUSPSExportEventListener
{

    private $uspsGateway;

    /**
     * AppDHLExportEventListener constructor.
     * @param $uspsGateway
     */
    public function __construct(USPSGateway $uspsGateway)
    {
        $this->uspsGateway = $uspsGateway;
    }

    /**
     * @param ExportShipmentEvent $event
     */
    public function exportShipment(ExportShipmentEvent $event)
    {
        $shippingExport = $event->getShippingExport();

        $shippingGateway = $shippingExport->getShippingGateway();
        $shipment = $shippingExport->getShipment();
        if ($shippingGateway->getCode() !== 'app_usps_shipping_gateway') {
            return;
        }
        try {
            $gatewayConfig = $shippingGateway->getConfig();
            $label = $this->uspsGateway->createLabel($gatewayConfig, $shipment);
        } catch (\Exception $exception) {
            $event->addErrorFlash(sprintf(
                "DHL Web Service for #%s order: %s",
                $shipment->getOrder()->getNumber(),
                $exception->getMessage()));

            return;
        }
        $event->addSuccessFlash(); // Add success notification
        $event->saveShippingLabel(base64_decode($label), 'pdf'); // Save label
        $event->exportShipment(); // Mark shipment as "Exported"
    }
}