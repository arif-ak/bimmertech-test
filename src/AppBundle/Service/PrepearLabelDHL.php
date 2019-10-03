<?php

namespace AppBundle\Service;

use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShippingExport;
use AppBundle\Entity\ShippingExportInterface;
use AppBundle\Entity\ShippingMethod;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGateway;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepository;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingGatewayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PrepearLabelDHL
 * @package AppBundle\Service
 */
class PrepearLabelDHL
{
    const FLASH_CREATE = 'Export shipping was created';
    const FLASH_UPDATE = 'Export shipping was updated';

    /**
     * @var ShippingGatewayRepository
     */
    private $repShippingGateway;

    /**
     * @var ShippingExportRepository
     */
    private $repShippingExport;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PrepearLabelDHL constructor.
     * @param ShippingGatewayRepository $repShippingGateway
     * @param ShippingExportRepository $repShippingExport
     * @param EntityManagerInterface $em
     */
    public function __construct(ShippingGatewayRepository $repShippingGateway,
                                ShippingExportRepository $repShippingExport, EntityManagerInterface $em)
    {
        $this->repShippingGateway = $repShippingGateway;
        $this->repShippingExport = $repShippingExport;
        $this->em = $em;
    }

    /**
     * @param $shipment
     * @return ShippingGateway|null
     */
    function getShipmentGateway(Shipment $shipment)
    {
        $shipmentGateway = null;
        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = $shipment->getMethod();
        /** @var ArrayCollection $shipmentGatewayArr */
        $shipmentGatewayArr = $this->repShippingGateway->findAll();
        /** @var ShippingGateway $gateway */
        foreach ($shipmentGatewayArr as $gateway) {
            foreach ($gateway->getShippingMethods() as $getwayMethod) {
                if ($getwayMethod->getCode() === $shippingMethod->getCode()) {
                    $shipmentGateway = $gateway;
                }
            }
        }

        return $shipmentGateway;
    }

    /**
     * @param Shipment $shipment
     * @param ShippingGateway $shipmentGateway
     * @return array
     */
    public function createShippingExport(Shipment $shipment, ShippingGateway $shipmentGateway)
    {
        $successFlash = '';
        $em = $this->em;
        $shipmentExportArr = $this->repShippingExport->findAll();

        $shipmentForSave = null;
        $needCreate = true;
        /** @var ShippingExport $export */
        foreach ($shipmentExportArr as $export) {
            if ($export->getShipment() === $shipment
                && $export->getShippingGateway() == $shipmentGateway
            ) {
                $export->setShippingGateway($shipmentGateway);
                $export->setShipment($shipment);
                $export->setExportedAt(null);
//                $export->setLabelPath(null);
                $export->setState(ShippingExportInterface::STATE_NEW);
                $shipmentForSave = $export;
                $shipment->setTracking('');
                $needCreate = false;
                $successFlash = $this::FLASH_UPDATE;
            }
        }

        if ($needCreate) {
            $shipmentExport = new ShippingExport();
            $shipmentExport->setShipment($shipment);
            $shipmentExport->setShippingGateway($shipmentGateway);
            $shipmentExport->setState(ShippingExportInterface::STATE_NEW);
            $shipmentForSave = $shipmentExport;
            $successFlash = $this::FLASH_CREATE;
        }

        $shipment->setShippingExport($shipmentForSave);
        $em->persist($shipment);
        $em->persist($shipmentForSave);
        $em->flush();

        return [$successFlash, $shipmentForSave];
    }
}
