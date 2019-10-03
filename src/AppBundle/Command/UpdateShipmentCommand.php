<?php

namespace AppBundle\Command;

use AfterShip\AfterShipException;
use AfterShip\Trackings;
use AppBundle\Entity\Order;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentInterface;
use AppBundle\Service\OrderService;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UpdateShipmentCommand
 * @package AppBundle\Command
 */
class UpdateShipmentCommand extends Command
{
    const AFTER_SHIP_NAME = [
        'dhl', "usps", "ems"
    ];

    const AFTER_SHIP_DELIVERED_STATUS = "Delivered";
    const SHIPMENT_NOT_EXIST = 4004;

    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var OrderService
     */
    private $orderService;


    /**
     * UpdateShipmentCommand constructor.
     * @param ShipmentRepository $shipmentRepository
     * @param ObjectManager $objectManager
     * @param ContainerInterface $container
     * @param OrderService $orderService
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        ObjectManager $objectManager,
        ContainerInterface $container,
        OrderService $orderService
    ) {
        parent::__construct('app:shipment-update');

        $this->shipmentRepository = $shipmentRepository;
        $this->objectManager = $objectManager;
        $this->container = $container;
        $this->orderService = $orderService;
    }

    /**
     * Set order state canceled after one month if order not paid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shipments = $this->shipmentRepository->findBy(['state' => Shipment::STATE_SHIPPED]);

        if ($shipments) {
            /** @var Shipment $shipment */
            foreach ($shipments as $shipment) {
                $shippingMethodName = $shipment->getMethod()->getCode();
                if ($shippingMethodName == ShipmentInterface::GENERAL_METHOD) {
                    $shippingMethodName = !empty($shipment->getCourier()) ? $shipment->getCourier() : "other";
                }

                $afterShipMethods = self::AFTER_SHIP_NAME;
                $shipmentExist = true;
                $isShipmentCreated = true;

                $filteredAfterShipMethods = [];
                foreach ($afterShipMethods as $key => $value) {
                    if (strpos($shippingMethodName, $value) !== false) {
                        $filteredAfterShipMethods[] = $value;
                    }
                }

                if (count($filteredAfterShipMethods) > 0 && $shipment->getTracking()) {
                    $valueOfAfterShip = $filteredAfterShipMethods[0];
                    $trackingNumber = $shipment->getTracking();
                    $tracking = new Trackings($this->container->getParameter('after_ship_key'));
                    try {
                        // find tracking by code
                        $response = $tracking->get($valueOfAfterShip, $trackingNumber);
                    } catch (AfterShipException $e) {
                        if ($e->getCode() == self::SHIPMENT_NOT_EXIST) {
                            $shipmentExist = false;
                            try {
                                // create tracking in a system of After ship
                                $tracking->create($trackingNumber, ['slug' => $valueOfAfterShip]);
                                $shipmentExist = true;
                            } catch (AfterShipException $e) {
                                $shipmentExist = false;
                            }

                            // wait 2 sec
                            sleep(2);
                        }
                    } finally {
                        if ($shipmentExist == true) {
                            // find tracking by code
                            if (empty($response)) {
                                $response = $tracking->get($valueOfAfterShip, $trackingNumber);
                            }

                            $data = isset($response['data']) ? $response['data'] : null;
                            $afterShipStatusOfDelivery = isset($data['tracking']) ? $data['tracking']['tag'] : null;
                            if ($afterShipStatusOfDelivery == self::AFTER_SHIP_DELIVERED_STATUS) {
                                $shipment->setState(Shipment::STATE_DELIVERED);
                                // send email with shipping
                                $this->container->get('app.service.order_shipment_email')->sendEmailWithDeliveredProducts($shipment);
                                /** @var Order $order */
                                $order = $shipment->getOrder();

                                $this->objectManager->persist($shipment);
                                $this->objectManager->persist($order);

                                $this->container->get('app.service.order_board_status')->
                                checkIsShippingDeliveredStatus($order);

                                $this->objectManager->flush();
                                // delete tracking in a system of After ship
                                $tracking->delete($valueOfAfterShip, $trackingNumber);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('check and change shipment state to delivered')
            ->setHelp('This command allows you check and change shipment status');
    }
}
