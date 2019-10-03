<?php

namespace AppBundle\EventListener;

use AppBundle\Service\DHLGateway;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AppDHLExportEventListener
 * @package AppBundle\EventListener
 */
final class AppDHLExportEventListener
{
    /**
     * @var DHLGateway
     */
    private $dhlGateway;

    /**
     * $filesystem
     */
    private $filesystem;

    /**
     * AppDHLExportEventListener constructor.
     * @param DHLGateway $dhlGateway
     * @param Filesystem $filesystem
     */
    public function __construct(DHLGateway $dhlGateway, Filesystem $filesystem)
    {
        $this->dhlGateway = $dhlGateway;
        $this->filesystem = $filesystem;
    }

    /**
     * @param ExportShipmentEvent $event
     */
    public function exportShipment(ExportShipmentEvent $event)
    {
        $shippingExport = $event->getShippingExport();

        $shippingGateway = $shippingExport->getShippingGateway();
        $shipment = $shippingExport->getShipment();
        if ($shippingGateway->getCode() !== 'app_dhl_shipping_gateway') {
            return;
        }

        try {
            $gatewayConfig = $shippingGateway->getConfig();
            $label = $this->dhlGateway->createLabel($gatewayConfig, $shipment);
        } catch (\Exception $exception) {
            $event->addErrorFlash(sprintf(
                "DHL Web Service for #%s order: %s",
                $shipment->getOrder()->getNumber(),
                $exception->getMessage()));
        }

        $path = $shippingExport ? $shippingExport->getLabelPath() : "";
        if ($path) {
            if ($this->filesystem->exists($path)) {
                $this->filesystem->remove($path);
            }
        }


//        $event->addSuccessFlash(); // Add success notification
        $event->saveShippingLabel(base64_decode($label), 'pdf'); // Save label
        $event->exportShipment(); // Mark shipment as "Exported"

            }
}
