<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderNote;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\Warehouse;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class OrderShipmentEmail
 * @package AppBundle\Service
 */
class OrderShipmentEmail
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $autChecker;

    /**
     * @var SenderInterface
     */
    private $sender;

    private $container;

    /**
     * OrderShipmentEmail constructor.
     * @param SenderInterface $sender
     * @param ObjectManager $objectManager
     * @param AuthorizationCheckerInterface $autChecker
     * @param Container $container
     */
    public function __construct(
        SenderInterface $sender,
        ObjectManager $objectManager,
        AuthorizationCheckerInterface $autChecker,
        Container $container
    ) {
        $this->sender = $sender;
        $this->objectManager = $objectManager;
        $this->autChecker = $autChecker;
        $this->container = $container;
    }

    /**
     * Send shipment email
     *
     * @param Order $order
     */
    public function sendShipmentEmail(Order $order)
    {
        $data = [
            'order' => $order,
            'trackingNumber' => $order->getNumber(),
            'couriers' =>$order->getShipments()->last()->getCourier()
        ];
        $this->send($order->getCustomer()->getEmail(), $data, 'app_order_shipment');

        if ($order->getShipments()->count() > 0) {

            /** @var Shipment $lastShipment */
            $lastShipment = $order->getShipments()->last();
//            if ($order->getShippingState() == 'shipped' || $order->getShippingState() == 'partially shipped') {
//                $data = [
//                    'order' => $order,
//                    'trackingNumber' => $lastShipment->getTracking(),
//                    'couriers' => $lastShipment->getCourier()
//                ];
//                $this->send($order->getCustomer()->getEmail(), $data, 'app_order_shipment');
//            }
        }
    }

    public function sendEmailWithDeliveredProducts(Shipment $shipment)
    {
        $order = $shipment->getOrder();
        if ($order->getCustomer()) {
            $shippedProducts = $this->getShipmentData($shipment);
            $this->send($order->getCustomer()->getEmail(), ['products' => $shippedProducts,
                'shipment' => $shipment,
                'order' => $order,
            ], 'app_order_delivered');
        }
    }

    public function sendEmailWithExpiredShipments(Shipment $shipment)
    {
        $order = $shipment->getOrder();
        $orderItemUnits = $this->objectManager->getRepository(OrderItemUnit::class)->
        findBy(['shipment' => $shipment->getId()]);

        $warehouses = [];
        /** @var OrderItemUnit $unit */
        foreach ($orderItemUnits as $unit) {
            if ($unit->getShipment()) {
                /** @var Warehouse $warehouse */
                $warehouse = $unit->getWarehouse();
                if ($warehouse && !in_array($warehouse->getId(), $warehouses)) {
                    $warehouses[] = $warehouse->getId();
                }
            }
        }

        $logisticUserArray = $this->objectManager->getRepository(UserLogistic::class)->createQueryBuilder('ul')
            ->andWhere('ul.warehouse IN (:warehouse)')
            ->setParameter('warehouse', $warehouses)
            ->getQuery()
            ->getResult();

        $adminUserArray = [];
        $adminUsers = $this->objectManager->getRepository(AdminUser::class)->findAll();
        foreach ($adminUsers as $user) {
            $role = $user->getRoles()[0];
            if ($role == AdminUser::DEFAULT_ADMIN_ROLE) {
                $adminUserArray[] = $user;
            }
        }

        $userArray = array_merge($logisticUserArray, $adminUserArray);

        foreach ($userArray as $user) {
            $role = $user->getRoles()[0];
            if ($role == UserLogistic::LOGISTIC_ROLE || $role == AdminUser::DEFAULT_ADMIN_ROLE) {
                $shippedProducts = $this->getShipmentData($shipment);
                $this->send($user->getEmail(), ['products' => $shippedProducts,
                    'trackingNumber' => $shipment->getTracking(),
                    'order' => $order
                ], 'app_order_shipment_expired');
            }
        }
    }

    /**
     * @param Order $object
     * @param $type
     */
    public function sendBackOrderEmail(Order $object, $type)
    {
        $userEmail = $object->getUser()->getEmail();
        $dataOrder = [
            'number' => $object->getNumber(),
            'name' => $object->getCustomer()->getFullName(),
        ];

        $this->send($userEmail, $dataOrder, $type);
    }

    /**
     * @param $cost
     * @param Order $object
     */
    public function sendOrderRefundEmail($cost, Order $object)
    {
        $userEmail = $object->getUser()->getEmail();
        $dataOrder = [
            'number' => $object->getNumber(),
            'cost' => $cost,
            'date' => date('Y-m-d H:i'),
            'name' => $object->getCustomer()->getFullName()
        ];

        $this->send($userEmail, $dataOrder, 'app_refund_order');
    }

    /**
     * @param OrderNote|null $orderNote
     * @param Order $object
     * @param $user
     * @throws \Exception
     */
    public function sendOrderNoteEmail(?OrderNote $orderNote, Order $object, $user)
    {
        $orderPersonals = $this->container->get('app.service.order_item_board_type_service')->
            getPersonalWithSupportOrder($object);

        /** @var AdminUser $orderPersonal */
        foreach ($orderPersonals as $orderPersonal) {
            $email = $orderPersonal->getEmail();
            $receiver = $orderPersonal->getFirstName() . " " . $orderPersonal->getLastName();
            $sender = $orderNote->getAuthor();
            $date = $orderNote->getCreatedAt()->format('Y-m-d H:i');
            $message = $orderNote->getMessage();

            /** @var AdminUser $user */
            $dataOrder = [
                'userEmail' => $user->getEmail(),
                'sender' => $sender,
                'receiver' => $receiver,
                'date' => $date,
                'number' => $object->getNumber(),
                'message' => $message,
            ];

            $this->send($email, $dataOrder, 'app_note_order');
        }
    }

    /**
     *  Send password and confirm email link
     *
     * @param $email
     * @param array $data
     * @param $type
     */
    public function send($email, array $data, $type)
    {
        if ($email) {
            $this->sender->send($type, [$email], ['data' => $data]);
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
