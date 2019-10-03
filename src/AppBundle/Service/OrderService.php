<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\PaymentInterface;
use AppBundle\Entity\PhotoReport;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentInterface;
use AppBundle\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OrderService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var ImageUploader
     */
    private $uploader;

    /**
     * ElasticSearchService constructor.
     *
     * @param EntityManagerInterface $em
     * @param Container $container
     * @param ImageUploader $uploader
     */
    public function __construct(
        EntityManagerInterface $em,
        Container $container,
        ImageUploader $uploader
    ) {
        $this->em = $em;
        $this->container = $container;
        $this->uploader = $uploader;
    }

    public function uploadImages($images, Shipment &$shipment)
    {
        foreach ($images as $image) {
            $photoReport = new PhotoReport();
            $photoReport->setFile($image);
            $photoReport->setOwner($shipment);
            $this->uploader->upload($photoReport);

            $this->em->persist($photoReport);
        }
    }

    public function removeImages($arrayImageIds, Shipment &$shipment)
    {
        $shipmentImages = $shipment->getImages();
        foreach ($arrayImageIds as $imageId) {
            foreach ($shipmentImages as $shipmentImage) {
                if ($shipmentImage->getId() == $imageId) {
                    $this->uploader->remove($shipmentImage->getPath());
                    $shipment->removeImage($shipmentImage);
                }
            }
        }
    }

    public function addUnits(&$shipment, &$orderItemUnits)
    {
        foreach ($orderItemUnits as $unit) {
            /** @var OrderItemUnit $unit */
            if (!$unit->getShipment()) {
                $shipment->addUnit($unit);
            } else {
                $id = $unit->getId();

                return new JsonResponse(['data' => "order_item_unit with id $id already used"], 400);
            }
        }
    }

    public function updateUnits(&$shipment, &$orderItemUnits)
    {
        /** @var OrderItemUnit $unit */
        foreach ($orderItemUnits as $unit) {
            if ($unit->getShipment()) {
                if ($unit->getShipment()->getId() != $shipment->getId()) {
                    return new JsonResponse(['error' => 'order_item_unit another shipment']);
                }
            }
        }

        // remove units
        /** @var OrderItemUnit $unit */
        foreach ($shipment->getUnits() as $unit) {
            /** @var OrderItemUnit $unit */
            $shipment->removeUnit($unit);
        }

        // add units
        foreach ($orderItemUnits as $unit) {
            /** @var OrderItemUnit $unit */
            $shipment->addUnit($unit);
        }
    }

    public function filePath($shippingExportObj)
    {
        $filePath = null;
        $explodeLabelPath = $shippingExportObj ? explode("/", $shippingExportObj->getLabelPath()) : '';
        if (count($explodeLabelPath) > 1) {
            $doc = end($explodeLabelPath);
            $filePath = '/shipping_labels/' . $doc;
        }

        return $filePath;
    }

    public function getError($error)
    {
        $message = $error[0];
        $error = [
            'code' => 400,
            'message' => $message
        ];

        return new JsonResponse(["errors" => $error], 400);
    }

    /**
     * @param ShippingExportInterface $shippingExport
     * @throws \Exception
     */
    public function dispatchExportShipmentEvent(ShippingExportInterface $shippingExport): void
    {
        $flashBag = $this->container->get('session')->getFlashBag();
        $shippingExportManager = $this->container->get('bitbag.manager.shipping_export');
        $eventDispatcher = $this->container->get('event_dispatcher');
        $filesystem = $this->container->get('filesystem');
        $translator = $this->container->get('translator');
        $rootDir = $this->container->get('kernel')->getProjectDir();
        $shippingLabelsPath = $rootDir . $this->container->getParameter('app.shipping_labels_path');

        $event = new ExportShipmentEvent(
            $shippingExport,
            $flashBag,
            $shippingExportManager,
            $filesystem,
            $translator,
            $shippingLabelsPath
        );

        $eventDispatcher->dispatch(ExportShipmentEvent::NAME, $event);
    }

    /**
     * @param Request $request
     * @return
     * @throws \Exception
     */
    public function exportSingleShipmentAction(Request $request)
    {
        $shippingExport = $this->container->get('bitbag.repository.shipping_export')->find($request->get('id'));
        $this->dispatchExportShipmentEvent($shippingExport);

        return $this->redirectToReferer($request);
    }



    /**
     * @param Order $order
     * @return null
     */
    public function isShippingAddressExist($order)
    {
        $shipAddress = $order->getShippingAddress();
        $number = $order->getNumber();
        if (!$shipAddress) {
            return new JsonResponse(['error' => "Shipping address for order with number: $number does not exist"], 400);
        }
    }

    /**
     * @param Shipment $shipment
     * @return array
     */
    public function getShipmentData(Shipment $shipment)
    {
        /** @var OrderItemUnit $orderItemUnits */
        $orderItemUnits = $shipment->getUnits();
        $arrayOrderItems = [];
        $result = [];

        /** @var OrderItemUnit $itemUnit */
        foreach ($orderItemUnits as $key => $itemUnit) {
            $itemId = $itemUnit->getOrderItem()->getId();
            if (!isset($arrayOrderItems['products'][$itemId]['product'])) {
                $arrayOrderItems['products'][$itemId]['product'] = $itemUnit->getOrderItem();
                $arrayOrderItems['products'][$itemId]['count'] = 1;
            } else {
                $arrayOrderItems['products'][$itemId]['count'] =
                    $arrayOrderItems['products'][$itemId]['count'] + 1;
            }
        }

        if (count($arrayOrderItems) > 0) {
            foreach ($arrayOrderItems['products'] as $item) {
                /** @var OrderItem $orderItem */
                $orderItem = $item['product'];
                $shippedQuantity = $item['count'];

                $result[] = [
                    'product' => $orderItem->getProduct(),
                    'quantity' => $shippedQuantity
                ];
            }
        }

        return $result;
    }
}
