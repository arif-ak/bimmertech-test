<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\UserLogistic;
use AppBundle\Form\Type\OrderItemCodingType;
use AppBundle\Form\Type\OrderItemInstructionType;
use AppBundle\Form\Type\OrderVinType;
use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\View\View;
use Sylius\Bundle\CoreBundle\Controller\OrderController as BaseOrderController;
use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactory;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class OrderController
 *
 * @package AppBundle\Controller
 */
class OrderController extends BaseOrderController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::INDEX);

        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);
        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    $this->metadata->getPluralName() => $resources,
                ]);
        }

        return $this->viewHandler->handle($configuration, $view);
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexRoleAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::INDEX);

        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);
        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    $this->metadata->getPluralName() => $resources,
                ]);
        }

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form
                ->handleRequest($request)->isValid()
        ) {
            $resource = $form->getData();


            /** @var ResourceControllerEvent $event */
            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

            // TODO: need fixed how find  that this form checkout address page. Can try get from state machine
            if ($request->request->get('sylius_checkout_address')) {
                $serviceCheckoutWarehouse = $this->get('app.service.checkout_warehouse');
                $serviceCheckoutWarehouse->addWarehouse($resource);
                $serviceCheckoutTaxes = $this->get('app.service.checkout_taxes');
                $serviceCheckoutTaxes->checkSetTaxes($resource);
            }
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
                $view = $configuration->getParameters()->get('return_content', false) ? View::create($resource,
                    Response::HTTP_OK) : View::create(null, Response::HTTP_NO_CONTENT);

                return $this->viewHandler->handle($configuration, $view);
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);

            if ($postEvent->hasResponse()) {
                return $postEvent->getResponse();
            }

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource);

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'));

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        /** @var Order $resource */
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);

        $shipments = $resource->getShipments();

        $view = View::create($resource);

        /** @var  Collection $products */
        $products = $resource->getItems();

        $orderItemForms = [];
        $instructionsForms = [];
        $codingForms = [];

//        $warehouseTable = $this->get('app.service.logistic_board_service')->preparerWarehouseTable($resource);
//
//        $logisticBoard = $this->get('app.service.logistic_board_service')->generateLogisticBoard($shipments);

        /** @var OrderItem $item */
        foreach ($products as $item) {
            $orderItemForms[] = $this->createForm(OrderItemType::class, $item)->createView();
            $instructionsForms[] = $this->createForm(OrderItemInstructionType::class, $item)->createView();
            $codingForms[] = $this->createForm(OrderItemCodingType::class, $item)->createView();
        }

        $orderVinForm = $this->createForm(OrderVinType::class, $resource);

        /** @var UserLogistic $user */
        $user = $this->getUser();

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                ->setTemplateVar($this->metadata->getName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $resource,
                    'itemForms' => $orderItemForms,
                    'instructionsForms' => $instructionsForms,
                    'orderVinForm' => $orderVinForm->createView(),
                    'userWareHouseId' => $userWarehouseId ?? null,
                    'isAdmin' => in_array('ROLE_ADMINISTRATION_ACCESS', $user->getRoles(), true),
                    $this->metadata->getName() => $resource,
                ]);
        }

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function checkVatNumberAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        /** @var Order $resource */
        $resource = $this->findOr404($configuration);
        $vatNumber = $request->request->get('vatNumber');
        $checkVatNumber = $this->get('app.service.check_vat_number');
        $validVat = $checkVatNumber->checkVatNumber($vatNumber);
        if (!$validVat) {
            $this->addFlash('error', "Sorry, but you enter invalid Vat number");
        } else {
            $this->addFlash('success', "Congratulation you don't need pay tax");
            $resource->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
            $resource->setVatNumber($vatNumber);
            $resource->recalculateAdjustmentsTotal();
            $resource->getBillingAddress()->setCompany($validVat['company_name']);
            $resource->getBillingAddress()->setCountryCode($validVat['country_code']);
            $fullAddress = $validVat['company_address'];
            $fullAddress = explode("\n", $fullAddress);
            $street = $fullAddress[0];
            $zipCity = explode(" ", $fullAddress[1]);
            $zip = $zipCity[0];
            $city = $zipCity[count($zipCity) - 1];
            $resource->getBillingAddress()->setStreet($street);
            $resource->getBillingAddress()->setPostcode($zip);
            $resource->getBillingAddress()->setCity($city);

            $this->container->get('sylius.manager.order')->flush();
        }

        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }

    /**
     * @param Request $request
     * @param int $shipmentId
     *
     * @return Response
     */
    public function prepearLabelAction(Request $request, int $shipmentId)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var Order $resource */
        $resource = $this->findOr404($configuration);

        $shipment = $this->getDoctrine()->getRepository(Shipment::class)->find($shipmentId);
        $prepearLabelService = $this->get('app.service.prepear_label_dhl');
        $shipmentGateway = $prepearLabelService->getShipmentGateway($shipment);

        if ($shipmentGateway !== null) {
            try {
                $successFlash = $prepearLabelService->createShippingExport($shipment, $shipmentGateway);
                $this->addFlash('success', $successFlash);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', "Sorry, but you dont have shipping gateway for this shipment");
        }

        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function sendInstructionAction(Request $request, int $id)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var Order $resource */
        $resource = $this->findOr404($configuration);
        $sendingItems = [];
        /** @var OrderItem $item */
        foreach ($resource->getItems() as $item) {
            if ($item->getInstruction() !== null
                && $item->getSupportStatus() !== OrderItemInterface::COMPLETE
            ) {
                $mailItem = [
                    'product' => $item->getProductName(),
                    'instruction' => $item->getInstruction(),
                    'item' => $item,
                ];
                array_push($sendingItems, $mailItem);
            }
        }
        if (count($sendingItems) > 0) {
            $email = $resource->getCustomer()->getEmail();
            $supportBoard = $this->get('app.service.support_board');
            $sending = $supportBoard->sendInstruction($sendingItems, $email);
            $supportBoard->supportStatus($resource);
            $this->addFlash($sending['status'], $sending['message']);
        } else {
            $this->addFlash('success', 'Nothing to send');
        }

        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }


    /**
     * Edit vin number for order
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateVin(Request $request)
    {

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);

        $form = $this->createForm(OrderVinType::class, $resource);
        $form->handleRequest($request);
        $resource = $form->getData();

        $em = $this->get('doctrine.orm.entity_manager');

        $em->persist($resource);
        $em->flush();

        return $this->redirectHandler->redirectToResource($configuration, $resource);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function summaryAction(Request $request): Response
    {
        return $this->render('@SyliusShop/Cart/summary.html.twig');
    }

    /**
     * @return array
     */
    protected function getWarrantyInfo()
    {

        $cart = $this->getCurrentCart();

        /* @var $repository \AppBundle\Repository\ProductRepository */
        $repository = $this->container->get('sylius.repository.product');

        $channel = $this->container->get('sylius.context.channel')->getChannel();
        $locale = $this->container->get('sylius.context.locale')->getLocaleCode();

        $warrantyMapping = [];

        $allWarranties = [];

        // iterate over all top level products, and collect all of them with warranty associations
        foreach ($cart->getItems() as $item) {
            if ($item->getParent() !== null) {
                continue;
            }

            foreach ($item->getProduct()->getAssociations() as $association) {
                if ($association->getType()->getCode() !== 'Warranty') {
                    continue;
                }

                $warrantyMapping[$item->getProduct()->getId()] = $item->getId();

            }
        }

        $hydrations = [
            'associations',
            'associations.associatedProducts',
            'associations.associatedProducts.variants',
            'associations.associatedProducts.variants.channelPricings' => ['channelCode' => $channel->getCode()],
        ];
        $res = $repository->findByIdsAndHydrate(array_keys($warrantyMapping), $hydrations, [], $channel, $locale);


        foreach ($res as $product) {
            foreach ($product->getAssociations() as $association) {
                if ($association->getType()->getCode() !== 'Warranty') {
                    continue;
                }

                $warrantyMapping[$product->getId()] = [];

                foreach ($association->getAssociatedProducts() as $warrantyProduct) {
                    $variant = $warrantyProduct->getVariants()->first();

                    if (!$variant) {
                        continue;
                    }

                    $allWarranties[] = $warrantyProduct->getId();

                    $warrantyMapping[$product->getId()][] = [
                        'id' => $variant->getId(),
                        'base_id' => $warrantyProduct->getId(),
                        'name' => $warrantyProduct->getName(),
                        'price' => $variant->getChannelPricingForChannel($channel),
                        'code' => $warrantyProduct->getCode(),
                    ];
                }

            }
        }

        return compact('warrantyMapping', 'allWarranties');
    }
}
