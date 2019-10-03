<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Form\Type\OrderItemInstructionType;
use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseOrderItemController;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Order\CartActions;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class OrderItemController
 * @package AppBundle\Controller
 */
class OrderItemController extends BaseOrderItemController
{

    public function addInstruction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        /** @var OrderItem $resource */
        $resource = $this->findOr404($configuration);

        $form = $this->createForm(OrderItemInstructionType::class, $resource);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid() && empty(!$resource->getInstruction())) {

            $resource->setSupportStatus($resource::NOT_SEND);
            $resource = $this->checkStatuses($resource);
            $em = $this->getDoctrine()->getManager();
            $em->persist($resource);
            $em->flush();

            return $this->redirectToRoute('sylius_admin_order_show', ['id' => $resource->getOrder()->getId()]);
        }
        return $this->redirectToRoute('sylius_admin_order_show', ['id' => $resource->getOrder()->getId()]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeWarehouseAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        /** @var OrderItem $resource */
        $resource = $this->findOr404($configuration);

        $orderItemUnits = $resource->getUnits();
        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $resource = $form->getData();
            $this->get('app.service.logistic_board_service')->changeWarehouseShipment($orderItemUnits);
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
        }
        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }

    /**
     *
     * @param OrderItem $orderItem
     * @return OrderItem
     */
    protected function checkStatuses(OrderItem $orderItem)
    {
        /** @var Order $order */
        $order = $orderItem->getOrder();

        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {

            if ($item->getSupportStatus() != $item::NOT_REQUIRED || $item->getSupportStatus() != $item::COMPLETED) {
                $order->setSupportStatus($item::PARTIALLY_ADDED);
            } else {
                $order->setSupportStatus($item::COMPLETED);
            }
        }

        return $orderItem;
    }
}
