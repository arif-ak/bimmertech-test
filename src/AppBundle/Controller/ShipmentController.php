<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Warehouse;
use AppBundle\Form\Type\WarehouseShipmentType;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ShipmentController
 * @package AppBundle\Controller
 */
class ShipmentController extends ResourceController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function createByWarehouseAction(Request $request, $id, $warehouse): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $shipment = new Shipment(true);
        $form = $this->createForm(WarehouseShipmentType::class, $shipment);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form
                ->handleRequest($request)->isSubmitted()
        ) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var Order $order */
            $order = $em->getRepository(Order::class)->find($id);
            /** @var Warehouse $warehouseEntity */
            $warehouseEntity = $em->getRepository(Warehouse::class)->find($warehouse);
            $units = $form->get('units')->getViewData();
            foreach ($units as $unit) {
                /** @var OrderItemUnit $unitEntity */
                $unitEntity = $em->getRepository(OrderItemUnit::class)->find($unit);
                $shipment->addUnit($unitEntity);
            }
            $shipment->setOrder($order);
            $shipment->setMethod($warehouseEntity->getShippingMethod()[0]);
            $em->persist($shipment);
            $em->flush();

            return $this->redirectHandler->redirectToResource($configuration, $shipment);
        }
        return $this->redirectHandler->redirectToResource($configuration, $shipment);
    }


    /**
     * @param Request $request
     * @param $id
     * @param $orderId
     * @return Response
     */
    public function addPhotoReportAction(Request $request, $id, $orderId): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        /** @var Shipment $resource */
        $resource = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $resource = $form->getData();

            /** @var ResourceControllerEvent $event */
            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToResource($configuration, $resource);
            }

            try {
                $this->resourceUpdateHandler->handle($resource, $configuration, $this->manager);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create($form, $exception->getApiResponseCode())
                    );
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource);

            if (!$configuration->isHtmlRequest()) {
                $view = $configuration->getParameters()->get('return_content', false) ? View::create($resource, Response::HTTP_OK) : View::create(null, Response::HTTP_NO_CONTENT);

                return $this->viewHandler->handle($configuration, $view);
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);

            if ($postEvent->hasResponse()) {
                return $postEvent->getResponse();
            }

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

//        if (!$configuration->isHtmlRequest()) {
//            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
//        }
//
//        $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource);

//        $view = View::create()
//            ->setData([
//                'configuration' => $configuration,
//                'metadata' => $this->metadata,
//                'resource' => $resource,
//                $this->metadata->getName() => $resource,
//                'form' => $form->createView(),
//            ])
//            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'));
//
//        return $this->viewHandler->handle($configuration, $view);

        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }
}