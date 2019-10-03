<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUsbCoding;
use AppBundle\Entity\OrderNote;
use AppBundle\Entity\OrderRefund;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentInterface;
use AppBundle\Entity\ShippingExport;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use AppBundle\Entity\Warehouse;
use AppBundle\Form\Type\API\ChangeOrderItemWarehouseType;
use AppBundle\Form\Type\API\ChangeOrderStateType;
use AppBundle\Form\Type\API\ChangeWarehouseType;
use AppBundle\Form\Type\API\CoddingBoardType;
use AppBundle\Form\Type\API\OrderItemInstructionType;
use AppBundle\Form\Type\API\OrderItemUsbCodingType;
use AppBundle\Form\Type\API\OrderRefundAndReturnType;
use AppBundle\Form\Type\API\ShipmentPrepareLabelType;
use AppBundle\Serializer\Normalizer\Order\NoteNormalizer;
use AppBundle\Serializer\Normalizer\Order\OrderStatesNormalizer;
use AppBundle\Serializer\Normalizer\Order\ProductListNormalizer;
use AppBundle\Serializer\Normalizer\WarehouseNormalizer;
use AppBundle\Service\ImageUploader;
use AppBundle\Service\OrderBoardStatusService;
use AppBundle\Service\OrderService;
use AppBundle\Utils\ValidationTrait;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use PayPal\Exception\PayPalConnectionException;

class OrderController extends Controller
{
    use ValidationTrait;

    /**
     * @var ImageUploader
     */
    protected $uploader;

    /**
     * @var ProductListNormalizer
     */
    protected $orderProductListNormalizer;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var OrderBoardStatusService
     */
    private $orderBoardStatusService;

    /**
     * @param ImageUploader $uploader
     * @param ProductListNormalizer $orderProductListNormalizer
     * @param OrderService $orderService
     * @param Filesystem $filesystem
     * @param OrderBoardStatusService $orderBoardStatusService
     */
    public function __construct(
        ImageUploader $uploader,
        ProductListNormalizer $orderProductListNormalizer,
        OrderService $orderService,
        Filesystem $filesystem,
        OrderBoardStatusService $orderBoardStatusService
    ) {
        $this->uploader = $uploader;
        $this->orderProductListNormalizer = $orderProductListNormalizer;
        $this->orderService = $orderService;
        $this->filesystem = $filesystem;
        $this->orderBoardStatusService = $orderBoardStatusService;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createSippingAction(Request $request): Response
    {
        $shipmentEmail = $this->get('app.service.order_shipment_email');
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        $requestData = $request->request->all();
        if (!empty($requestData['tracking_number'])
            && !empty($requestData['courier'])
        ) {
            $foreignShipment = true;
        }

        $form = $this->createForm(ShipmentPrepareLabelType::class, null,
            ['shipment' => false, 'foreignShipment' => isset($foreignShipment) ? $foreignShipment : false]
        );

        $form->handleRequest($request);
        $data = $form->getData();

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        /** @var OrderItem $orderItem */
        /** @var OrderItemUnit $orderItemUnit */
        $orderItemUnits = count($data['order_item_units']) > 0 ? $data['order_item_units'] : null;
        $order = $data['order_id'];

        if (!$order->getShippingAddress()) {
            return $this->orderService->isShippingAddressExist($order);
        }

        $shipment = new Shipment(true);
        $shipment->setShipMethod($data['ship_method_id']);
        $shipment->setOrder($order);

        if (isset($foreignShipment) && $foreignShipment) {
            $shipment->setTracking($data['tracking_number']);
            $shipment->setCourier($data['courier']);
        } else {
            $shipment->setDhlInsuredAmount($data['insured_amount']);
            $shipment->setDhlNumberOfPieces($data['number_of_pieces']);
            $shipment->setDhlWeight($data['dhl_weight']);
            $shipment->setWidth($data['width']);
            $shipment->setHeight($data['height']);
            $shipment->setWidth($data['length']);

            $prepareLabelService = $this->get('app.service.prepear_label_dhl');
            $shipmentGateway = $prepareLabelService->getShipmentGateway($shipment);

            if ($shipmentGateway !== null) {
                try {
                    $shippingExportObj = null;
                    list($message, $shippingExportObj) =
                        $prepareLabelService->createShippingExport($shipment, $shipmentGateway);
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            } else {
                return $this->getErrorResponse("Shipment gateway not found");
            }

            if (count($error = $session->getFlashBag()->get('error')) > 0) {
                return $this->orderService->getError($error);
            }

            $this->orderService->dispatchExportShipmentEvent($shippingExportObj);

            if (count($error = $session->getFlashBag()->get('error')) > 0) {
                return $this->orderService->getError($error);
            }
        }

        // upload images
        $this->orderService->uploadImages($data['images'], $shipment);

        // add shipment to units
        $this->orderService->addUnits($shipment, $orderItemUnits);

        // change shipping status
        $this->orderBoardStatusService->checkShippingStatus($order, $shipment);

        $em->persist($shipment);
        $em->flush();

        $shipmentEmail->sendShipmentEmail($order);

        if (isset($shippingExportObj)) {
            $filePath = $this->orderService->filePath($shippingExportObj);
            $data = [
                'label' => $filePath ? $filePath : "",
                "tracking_number" => $shipment->getTracking()
            ];

            return new JsonResponse(['data' => $data], 200);
        } else {
            return new JsonResponse(['data' =>
                ["message" => "Shipment was successfully created",
                    "tracking_number" => $shipment->getTracking()
                ]
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function updateSippingAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        $requestData = $request->request->all();

        $form = $this->createForm(ShipmentPrepareLabelType::class, null,
            [
                'shipment' => true,
                'foreignShipment' => true,
                'isCreate' => false
            ]
        );

        $form->handleRequest($request);
        $data = $form->getData();

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        /** @var OrderItem $orderItem */
        /** @var OrderItemUnit $orderItemUnit */
//        $orderItemUnits = count($data['order_item_units']) > 0 ? $data['order_item_units'] : null;
        $order = $data['order_id'];

        if (!$order->getShippingAddress()) {
            return $this->orderService->isShippingAddressExist($order);
        }

        /** @var Shipment $shipment */
        $shipment = $data['shipment_id'];

        $trackingNumber = $shipment->getTracking();
        if ($data['tracking_number'] != $trackingNumber) {
            $shipment->setTracking($data['tracking_number']);
        }

        // upload images
        $this->orderService->uploadImages($data['images'], $shipment);
        // remove images
        $this->orderService->removeImages($data['removed_image_ids'], $shipment);

        if (count($error = $session->getFlashBag()->get('error')) > 0) {
            return $this->orderService->getError($error);
        }

        // add shipment to units
//        $this->orderService->updateUnits($shipment, $orderItemUnits);

        // change shipping status
        $this->orderBoardStatusService->checkShippingStatus($order, $shipment);

        $em->persist($shipment);
        $em->flush();

        $shippingExportObj = $this->getDoctrine()->
        getRepository(ShippingExport::class)->findOneBy(["shipment" => $shipment->getId()]);
        if ($data['tracking_number'] != $trackingNumber && $shippingExportObj) {
            $path = $shippingExportObj ? $shippingExportObj->getLabelPath() : "";
            if ($path) {
                if ($this->filesystem->exists($path)) {
                    $this->filesystem->remove($path);
                }
            }
            $shippingExportObj->setShipment(null);
            $shippingExportObj->setLabelPath(null);
            $em->flush();
        }

        if ($data['tracking_number'] == $trackingNumber && $shippingExportObj) {
            $filePath = $this->orderService->filePath($shippingExportObj);
            $data = [
                'label' => $filePath ? $filePath : "",
                "tracking_number" => $shipment->getTracking()
            ];

            return new JsonResponse(['data' => $data], 200);
        } else {
            return new JsonResponse(['data' =>
                ["message" => "Shipment was successfully updated",
                    "tracking_number" => $shipment->getTracking()
                ]
            ], 200);
        }
    }

    public function warehouseOrderItemsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = [];
        $userWarehouseAccess = [];
        $usbCodingBoardAccess = [];
        $showLogisticBoard = true;
        $order = $em->getRepository(Order::class)->find($id);
        $warehouses = $em->getRepository(Warehouse::class)->findAll();

        /** @var UserLogistic $user */
        $user = $this->getUser();
        if ($this->get('security.authorization_checker')->isGranted(UserLogistic::LOGISTIC_ROLE)) {
            $userWarehouse = $user->getWarehouse() ? $user->getWarehouse() : null;
            $userWarehouseAccess = [$userWarehouse->getName()];
            $usbCodingBoardAccess = [$userWarehouse->getName()];

            $showLogisticBoard = true;
        } elseif ($this->get('security.authorization_checker')->isGranted(UserLogistic::DEFAULT_ADMIN_ROLE)) {
            foreach ($warehouses as $warehouse) {
                $userWarehouseAccess[] = $warehouse->getName();
                $usbCodingBoardAccess[] = $warehouse->getName();
            }

            $showLogisticBoard = true;
        } elseif ($this->get('security.authorization_checker')->isGranted(UserCoding::CODING_ROLE) ||
            $this->get('security.authorization_checker')->isGranted(UserSupport::SUPPORT_ROLE)
        ) {
            foreach ($warehouses as $warehouse) {
                $usbCodingBoardAccess[] = $warehouse->getName();
            }
        }

        if ($showLogisticBoard) {
            $warehouseTable = $this->get('app.service.logistic_board_service')->warehouseOrderItems($order);

            $result = $this->get('app.serializer_normalizer.warehouse_order_item_normalizer')
                ->normalize($warehouseTable, null, [
                    "logistic_board_access" => $userWarehouseAccess,
                    "usb_coding_board_access" => $usbCodingBoardAccess,
                    "vin" => $order->getVin()
                ]);
        }
        /** @var OrderItem $item */
        $item = $order->getPaymentState();

        return new JsonResponse(['data' => $result]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getShipmentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$shipping = $em->getRepository(Shipment::class)->find($id)) {
            return new JsonResponse('Shipping not found', 404);
        }

        $shippingExportObj = $this->getDoctrine()->
        getRepository(ShippingExport::class)->findOneBy(["shipment" => $shipping->getId()]);

        $shippingResponse = $this->get('app.serializer_normalizer.shipping_detail')->
        normalize($shipping, null, ['shippingExportObj' => $shippingExportObj]);

        return new JsonResponse(['data' => $shippingResponse]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function orderProductListsAction(Request $request, $id)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $em = $this->getDoctrine()->getManager();

        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return new JsonResponse(['error' => "order not found"], 500);
        }

        $data = $this->orderProductListNormalizer->normalize($order, null, ['channel' => $channel]);

        return new JsonResponse(['data' => $data]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function supportBoardListAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return new JsonResponse(['error' => "order not found"], 500);
        }

        $supportBoard = $this->get('app.service.support_board');
        $orderItems = $supportBoard->isOrderItemsArePhysicalAndInstructionRequired($order);

        $data = $this->container->
        get("app.serializer_normalizer.order_support_board_list_normalizer")->normalize($orderItems);

        return new JsonResponse(['data' => $data]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function addInstructionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $supportBoard = $this->get('app.service.support_board');

        $form = $this->createForm(OrderItemInstructionType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }
        $data = $form->getData();

        $completedInstruction = [];

        foreach ($data['order_items'] as $value) {
            /** @var OrderItem $item */
            $item = $value;

            $item->setSupportStatus(OrderItemInterface::COMPLETE);
            $item->setInstruction($data['instruction']);
            $item->setSupportDate(new \DateTime('now'));

            $em->persist($item);
            if (!empty($data['instruction']) && $item->getSupportStatus() == OrderInterface::STATUS_COMPLETE) {
                $completedInstruction[] = [
                    'product' => $item->getProductName(),
                    'instruction' => $item->getInstruction(),
                    'item' => $item,
                ];
            }
        }

        /** @var Order $order */
        $order = $data['order_id'];
        $email = $order->getCustomer()->getEmail();

        $order = $this->orderBoardStatusService->checkSupportStatuses($order);

        if ($data['send_email'] == false) {
            $supportBoard->sendInstructionAPI($completedInstruction, $email);
        }

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['data' => 'success']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function coddingBoardListAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return new JsonResponse(['error' => "order not found"], 500);
        }

        $data = $this->get("app.serializer_normalizer.order_codding_board_list_normalizer")->normalize($order);

        return new JsonResponse(['data' => $data]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function addCoddingAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return new JsonResponse(['error' => "order not found"], 500);
        }

        $form = $this->createForm(CoddingBoardType::class);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $data = $form->getData();
        /** @var OrderItem $orderItem */
        $orderItem = isset($data['order_item_id']) ? $data['order_item_id'] : null;

        if ($orderItem) {
            $orderItem->setCodingStatus($data['status']);
            $orderItem->setCodingDate(new \DateTime("now"));
            $em->persist($orderItem);
        } else {
            return new JsonResponse(['error' => "required parameters doesn't exist"]);
        }

        $this->orderBoardStatusService->checkCodingStatuses($order, true);
        $em->flush();

        return new JsonResponse(['data' => "codding status successful updated"]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function removeShipmentAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$shipment = $em->getRepository(Shipment::class)->find($id)) {
            return new JsonResponse(['error' => "shipment not found"], 500);
        }

        $order = $shipment->getOrder();
        // remove units
        /** @var OrderItemUnit $unit */
        foreach ($shipment->getUnits() as $unit) {
            /** @var OrderItemUnit $unit */
            $shipment->removeUnit($unit);
        }

        $this->orderBoardStatusService->checkShippingStatus($order);

        $em->remove($shipment);
        $em->flush();

        return new JsonResponse(["data" => "shipment was successfully deleted"]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeWareHouseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ChangeWarehouseType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $orderItemUnits = $data['order_item_units'];
        $warehouse = $data['warehouse_id'];
        /** @var Order $order */
        $order = $data['order_id'];

        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            $unitOrder = $orderItemUnit->getOrderItem()->getOrder();
            if ($unitOrder->getId() == $order->getId()) {
                $orderItemUnit->setWarehouse($warehouse);
            } else {
                return new JsonResponse(['error' => "order_item_unit does not from this order"]);
            }
        }

        $em->flush();

        return new JsonResponse(["data" => "order_item_units was successfully updated"]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function warehouseList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $warehouseList = $em->getRepository(Warehouse::class)->findAll();

        $result = [];
        foreach ($warehouseList as $warehouse) {
            $result[] = (new WarehouseNormalizer())->normalize($warehouse);
        }

        return new JsonResponse(['data' => $result], 200);
    }

    /**
     * @param Request $request
     * @param $variable
     *
     * @return JsonResponse
     */
    public function updateStateAction(Request $request, $variable)
    {
        $em = $this->getDoctrine()->getManager();
        $request->request->set('state_variable', $variable);
        $value = $request->request->get('value');
        $form = $this->createForm(ChangeOrderStateType::class,
            ['state_variable' => $request->request->get('state_variable')]);

        $form->handleRequest($request);
        $data = $form->getData();

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        /** @var Order $order */
        $order = $data['order_id'];

        switch ($variable) {
            case OrderInterface::ORDER_GENERAL_STATE:
                $order->setState($value);
                break;
            case OrderInterface::ORDER_CODDING_STATE:
                $order->setCodingStatus($value);
                break;
            case OrderInterface::ORDER_SUPPORT_STATE:
                $order->setSupportStatus($value);
                break;
            case OrderInterface::ORDER_PAYMENT_STATE:
                $order->setPaymentState($value);
                break;
            case OrderInterface::ORDER_SHIPMENT_STATE:
                $this->get('app.service.order_shipment_email')->sendBackOrderEmail($order, 'app_change_back_order_status');
                $order->setShippingState($value);
                break;
        }

        $this->orderBoardStatusService->checkOrderStatus($order);

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['data' => "Order state was successfully updated"], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getStateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => (new OrderStatesNormalizer())->normalize($order)], 200);
    }

    public function getUserRole($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return new JsonResponse(['error' => "order not found"], 404);
        }

        $role = "";
        if ($this->get('security.authorization_checker')->isGranted(UserLogistic::LOGISTIC_ROLE)) {
            $role = UserLogistic::LOGISTIC_ROLE;
        } elseif ($this->get('security.authorization_checker')->isGranted(UserSupport::SUPPORT_ROLE)) {
            $role = UserSupport::SUPPORT_ROLE;
        } elseif ($this->get('security.authorization_checker')->isGranted(UserCoding::CODING_ROLE)) {
            $role = UserCoding::CODING_ROLE;
        } elseif ($this->get('security.authorization_checker')->isGranted(AdminUser::DEFAULT_ADMIN_ROLE)) {
            $role = AdminUser::DEFAULT_ADMIN_ROLE;
        }

        return new JsonResponse(['data' => [
            'role' => $role,
            'VIN' => $order->getVin()
        ]]);
    }

    /**
     * Get order payment status
     *
     * @param $id
     * @return JsonResponse
     */
    public function orderPaymentStatus($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $data = ['paymentStatus' => $order->getPaymentState()];
        return new JsonResponse($data);
    }

    /**
     * Get order payment status
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function orderChangeVIN(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $vin = $request->get('vin');
        if (!empty($vin)) {
            $order->setVin($vin);
            $em->flush();
        } else {
            return $this->getErrorResponse('VIN number not sent');
        }

        return new JsonResponse(['data' => ['status' => 'updated']], JsonResponse::HTTP_OK);
    }

    /**
     * Get order comment
     *
     * @param $id
     * @return JsonResponse
     */
    public function orderComment($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $comment = $order->getNotes();

        return new JsonResponse(['data' => ['comment' => $comment]], JsonResponse::HTTP_OK);
    }

    /**
     * Create order note
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function orderNoteCreate(Request $request, $id)
    {
        $data = $request->request->all();
        $date = new \DateTime('now');
        /** @var AdminUser $user */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        if (isset($data['message']) && !empty($data['message'])) {
            $orderNote = new OrderNote();
            $orderNote->setAuthor($user->getFirstName() . " " . $user->getLastName());
            $orderNote->setCreatedAt($date);
            $orderNote->setOrder($order);
            $orderNote->setMessage($data['message']);

            $em->persist($orderNote);
            $em->flush();

            $this->get('app.service.order_shipment_email')->sendOrderNoteEmail($orderNote, $order, $user);
        } else {
            return $this->getErrorResponse('Required parameter "message" not found', JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['data' => "Note was successfully created"], JsonResponse::HTTP_OK);
    }

    /**
     * Get order comment
     *
     * @param $id
     * @return JsonResponse
     */
    public function orderNoteIndex($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $orderNotes = $em->getRepository(OrderNote::class)->
        findBy(['order' => $order->getId()], ['id' => 'DESC']);
        $result = (new NoteNormalizer())->normalize($orderNotes);

        return new JsonResponse(['data' => $result], JsonResponse::HTTP_OK);
    }

    public function sendUsbProduct(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(OrderItemUsbCodingType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $data = $form->getData();
        /** @var Order $order */
        $order = $data['order_id'];

        $usbCoding = null;
        /** @var OrderItem $orderItem */
        foreach ($data['order_items'] as $orderItem) {
            $product = $orderItem->getProduct();

            if ($orderItem->getOrderItemUsbCoding() && $product->getType() != ProductInterface::TYPE_USB_CODING) {
                return $this->getErrorResponse('This product already coded or hasn\'t from usb coding', JsonResponse::HTTP_BAD_REQUEST);
            }

            if (!$orderItem->getOrderItemUsbCoding()) {
                $usbCoding = new OrderItemUsbCoding();
                $usbCoding->setIsCoded(true);
                $usbCoding->setCreatedAt(new \DateTime('now'));
                $usbCoding->setOrderItem($orderItem);

                $em->persist($usbCoding);
                $listOrderItem = $order->getItems();
                foreach ($listOrderItem as $item) {
                    /** @var OrderItem $item */
                    if ($item == $orderItem) {
                        $item->setOrderItemUsbCoding($usbCoding);
                    }
                }
            }
        }

        $this->orderBoardStatusService->checkUsbCodingBoardStatus($order, true);

        $em->flush();

        return new JsonResponse(['data' => 'successfully created']);
    }

    public function editUsbProduct(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(OrderItemUsbCodingType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $data = $form->getData();
        /** @var Order $order */
        $order = $data['order_id'];

        /** @var OrderItem $orderItem */
        foreach ($data['order_items'] as $orderItem) {
            $product = $orderItem->getProduct();
            $usbCoding = $orderItem->getOrderItemUsbCoding();

            if (!$usbCoding && $product->getType() != ProductInterface::TYPE_USB_CODING) {
                return $this->getErrorResponse('This product was not coded or hasn\'t from usb coding', JsonResponse::HTTP_BAD_REQUEST);
            }

            $listOrderItem = $order->getItems();
            foreach ($listOrderItem as $item) {
                /** @var OrderItem $item */
                if ($item == $orderItem) {
                    $item->setOrderItemUsbCoding(null);
                }
            }

            $em->remove($usbCoding);
        }

        $this->orderBoardStatusService->checkUsbCodingBoardStatus($order, true);

        $em->flush();

        return new JsonResponse(['data' => 'successfully updated']);
    }

    public function changeWarehouseForUSbCoding(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ChangeOrderItemWarehouseType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $orderItems = $data['order_items'];
        $warehouse = $data['warehouse_id'];
        /** @var Order $order */
        $order = $data['order_id'];

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            $orderItemOrder = $orderItem->getOrder();
            if ($orderItemOrder->getId() == $order->getId()) {
                $orderItem->setWarehouse($warehouse);
            } else {
                return new JsonResponse(['error' => "order_item does not from this order"]);
            }
        }

        $em->flush();

        return new JsonResponse(["data" => "order_item was successfully updated"]);
    }

    public function orderReturn(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($data['order_id'])) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $orderItemUnits = $em->getRepository(OrderItemUnit::class)->getByShipmentAndWarehouse($order->getId());

        $data = [];
        $data['order_item_units_free'] = $this->get("app.normalizer.order_item_unit_return")->normalize($orderItemUnits);
        $data['order_item_units_returned'] = $this->get("app.normalizer.order_item_unit_return")->
        normalize($orderItemUnits, null, ["check_is_order_item_returned" => true]);

        return new JsonResponse(['data' => $data], 200);
    }

    public function orderBalance(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();

        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($data['order_id'])) {
            return $this->getErrorResponse('Order not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $orderIteUnits = $em->getRepository(OrderItemUnit::class)->getByShipmentAndWarehouse($order->getId());
        $result = $this->get('app.normalizer.order_balance')->normalize($orderIteUnits);

        return new JsonResponse(['data' => $result], 200);
    }

    public function orderReturnAndRefundCreate(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(OrderRefundAndReturnType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $data = $form->getData();

        /** @var Order $order */
        $order = $data['order_id'];

        if (!$this->get('app.service.order_refund')->validateRefundTotal($data)) {
            return $this->getErrorResponse("Parameters of refund are invalid");
        }

        $orderItemUnits = $em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($data['order_id']->getId());

        $orderRefund = $this->get("app.service.order_refund")->createOrderRefund($data);
        $this->get("app.service.order_refund")->saveRefundData($data, $orderRefund);
        $this->get("app.service.order_refund")->saveReturnData($data, $orderItemUnits, $orderRefund);

        if (!$transactionId = $this->get("app.service.paypal")->getTransactionId($data["order_id"])) {
            return $this->getErrorResponse("Transaction id not found");
        }

        $cost = number_format($data['total'] / 100, 2, ".", "");
        $floatCost = (float)$cost;

        if (isset($data['order_item_unit_refund']) && count($data['order_item_unit_refund']) > 0) {
            $sale = $this->get("app.service.paypal")->getSale($transactionId);
            $refundRequest = $this->get("app.service.paypal")->refundRequest($floatCost);

            try {
                $apiContext = $this->get("app.service.paypal")->getApiContext();
                $refundedSale = $sale->refundSale($refundRequest, $apiContext);

                $em->flush();

                $this->get('app.service.order_shipment_email')->sendOrderRefundEmail($cost, $order);
            } catch (PayPalConnectionException $ex) {
                return new JsonResponse($ex->getMessage() . " " . "code: " . "over limit of refund", 400);
            }
        } else {
            $em->flush();
        }

        return new JsonResponse('Your transaction was succesful');
    }

    public function orderReturnAndRefundEdit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(OrderRefundAndReturnType::class, null, ['is_create' => false]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->createValidationErrorsResponse($form);
        }

        $data = $form->getData();

        $orderItemUnits = $em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($data['order_id']->getId());

        $orderRefund = $this->get("app.service.order_refund")->createOrderRefund($data, false);
        $this->get("app.service.order_refund")->saveReturnData($data, $orderItemUnits, $orderRefund);

        $em->flush();

        return new JsonResponse('Your transaction was succesful updated');
    }

    public function getOrderRefundAndReturn(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var OrderRefund $orderRefund */
        if (!$orderRefund = $em->getRepository(OrderRefund::class)->find($id)) {
            return $this->getErrorResponse('Order refund not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $orderItemUnits = $em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($orderRefund->getOrder()->getId());

        $result = $this->get('app.normalizer.order_refund')
            ->normalize($orderRefund, null, [
                "order_item_units" => $orderItemUnits,
                "showItem" => true
            ]);

        return new JsonResponse(['data' => $result], 200);
    }

    public function getOrderRefundIndex(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Order $order */
        if (!$order = $em->getRepository(Order::class)->find($id)) {
            return $this->getErrorResponse('Order refund not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $orderItemUnits = $em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($order->getId());

        $orderRefunds = $order->getOrderRefund()->count() > 0 ? $order->getOrderRefund() : [];

        $result = [];
        foreach ($orderRefunds as $orderRefund) {
            $result[] = $this->get('app.normalizer.order_refund')
                ->normalize($orderRefund, null, [
                    "order_item_units" => $orderItemUnits,
                    "showItem" => false
                ]);
        }

        return new JsonResponse(['data' => $result], 200);
    }

    public function boardAccess(Request $request)
    {
        /** @var UserLogistic $user */
        $user = $this->getUser();
        $data = [];
        if ($user) {
            $data = $this->get("app.serializer_normalizer.order_access_normalizer")->normalize($user);
        }

        return new JsonResponse(["data" => $data], Response::HTTP_OK);
    }
}
