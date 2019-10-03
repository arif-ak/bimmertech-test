<?php

namespace AppBundle\Service;

use AppBundle\Entity\Shipment;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use USPS\InternationalLabel;

/**
 * Class USPSGateway
 * @package AppBundle\Service
 */
class USPSGateway
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DHLGateway constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $gatewayConfig
     * @param Shipment $shipment
     * @return mixed
     */
    public function createLabel(array $gatewayConfig, ShipmentInterface $shipment)
    {
        /** @var Order $order */
        $order = $shipment->getOrder();
        $shipAddress = $order->getShippingAddress();
        // Initiate and set the username provided from usps
        $label = new InternationalLabel($gatewayConfig['username']);

        $label->setApiVersion($gatewayConfig['apiVersion']);

        $label->setTestMode($gatewayConfig['mode']);
        $label->setFromAddress($gatewayConfig['firstName'], $gatewayConfig['lastName'],
            $gatewayConfig['company'], $gatewayConfig['address'], $gatewayConfig['city'],
            $gatewayConfig['countryCode'], $gatewayConfig['postalCode'], $gatewayConfig['address2']);
        $label->setToAddress('Vincent', 'Gabriel', '5440 Tujunga Ave', 'North Hollywood', 'CA', 'US', '91601', '# 707');

        $label->setWeightOunces(1);
        $label->addContent('Automotive parts', '10', 0, 10);

        $label->createLabel();
        if ($label->isSuccess()) {
//            echo 'Done';
//            echo "\n Confirmation:" . $label->getConfirmationNumber();
////            $label = $label->getLabelContents();

//                $contents = base64_decode($label);

            $labelReade = $label->getLabelContents();
            $shipment->setTracking($label->getConfirmationNumber());

            $this->em->persist($shipment);
            $this->em->flush();

        } else {
            $label->getErrorMessage();
//            TODO: need create own exception
            throw  new Exception();
        }
        return $labelReade;
    }
}