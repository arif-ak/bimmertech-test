<?php

namespace AppBundle\Command;

use AfterShip\AfterShipException;
use AfterShip\Trackings;
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
 * Class CheckShipmentCommand
 * @package AppBundle\Command
 */
class CheckShipmentCommand extends Command
{
    const AFTER_SHIP_NAME = [
        'dhl', "usps", "ems"
    ];

    const AFTER_SHIP_PENDING_STATUS = "Pending";
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
        parent::__construct('app:shipment-check-pending');

        $this->shipmentRepository = $shipmentRepository;
        $this->objectManager = $objectManager;
        $this->container = $container;
        $this->orderService = $orderService;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws AfterShipException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shipments = $this->shipmentRepository->findBy(['state' => Shipment::STATE_SHIPPED]);

        if ($shipments) {
            /** @var Shipment $shipment */
            foreach ($shipments as $shipment) {
                $afterShipMethods = self::AFTER_SHIP_NAME;
                $shippingMethodName = $shipment->getMethod()->getCode();
                if ($shippingMethodName == ShipmentInterface::GENERAL_METHOD) {
                    $shippingMethodName = !empty($shipment->getCourier()) ? $shipment->getCourier() : "other";
                }

                $filteredAfterShipMethods = [];
                foreach ($afterShipMethods as $key => $value) {
                    if (strpos($shippingMethodName, $value) !== false) {
                        $filteredAfterShipMethods[] = $value;
                    }
                }

                if (count($filteredAfterShipMethods) && $shipment->getTracking()) {
                    $valueOfAfterShip = $filteredAfterShipMethods[0];
                    $trackingNumber = $shipment->getTracking();
                    try {
                        $tracking = new Trackings($this->container->getParameter('after_ship_key'));
                        // find tracking by code
                        $response = $tracking->get($valueOfAfterShip, $trackingNumber);

                        $data = isset($response['data']) ? $response['data'] : null;
                        $afterShipStatusOfDelivery = isset($data['tracking']) ? $data['tracking']['tag'] : null;
                        if ($afterShipStatusOfDelivery == self::AFTER_SHIP_PENDING_STATUS) {
                            $afterShipDate = isset($data['tracking']) ?
                                new \DateTime($data['tracking']['created_at']) : null;
                            $modifyAfterShipDate = $afterShipDate->modify('+14 day');
                            $currentDate = new \DateTime('now');
                            if ($currentDate > $modifyAfterShipDate) {
                                $shipment->setState(ShipmentInterface::STATE_EXPIRED);
                                $this->objectManager->persist($shipment);
                                $this->objectManager->flush();

                                $this->container->get('app.service.order_shipment_email')
                                    ->sendEmailWithExpiredShipments($shipment);
                                // delete tracking in a system of After ship
                                $tracking->delete($valueOfAfterShip, $trackingNumber);
                            }
                        }
                    } catch (AfterShipException $e) {
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
