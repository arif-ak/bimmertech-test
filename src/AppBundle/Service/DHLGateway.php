<?php

namespace AppBundle\Service;

use AppBundle\Entity\Shipment;
use DateTime;
use DHL\Client\Web as WebserviceClient;
use DHL\Datatype\GB\Piece;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Entity\GB\ShipmentResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Intl\Intl;

/**
 * Class DHLGateway
 * @package AppBundle\Service
 */
class DHLGateway
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
        $consigneeName = $shipAddress->getFirstName() . ' ' . $shipAddress->getLastName();

        $dhlSample = new ShipmentRequest();
        $dhlSample->SiteID = $gatewayConfig['dhlId'];
        $dhlSample->Password = $gatewayConfig['pass'];

        $myTime = new DateTime();
        $myTime = $myTime->format(DateTime::RFC3339_EXTENDED);

        $dhlSample->MessageTime = $myTime;
        $dhlSample->MessageReference = substr(uniqid(rand(), true), 0, 32);

        $dhlSample->RegionCode = $gatewayConfig['regionCode'];
        $dhlSample->LanguageCode = 'en';
        $dhlSample->PiecesEnabled = 'Y';
        $dhlSample->Billing->ShipperAccountNumber = $gatewayConfig['shipperAccountNumber'];
        $dhlSample->Billing->ShippingPaymentType = 'S';
        $dhlSample->Billing->DutyPaymentType = 'R';
        if ($shipAddress->getCompany() !== '' && $shipAddress->getCompany() !== null) {
            $dhlSample->Consignee->CompanyName = $shipAddress->getCompany();
        } else {
            $dhlSample->Consignee->CompanyName = $consigneeName;
        }
        $dhlSample->Consignee->AddressLine = $shipAddress->getStreet();
        $dhlSample->Consignee->City = $shipAddress->getCity();
        if ($shipAddress->getPostcode() !== '' && $shipAddress->getPostcode() !== null) {
            $dhlSample->Consignee->PostalCode = $shipAddress->getPostcode();
        }
        $dhlSample->Consignee->CountryCode = $shipAddress->getCountryCode();
        $dhlSample->Consignee->CountryName = Intl::getRegionBundle()->
        getCountryName($shipAddress->getCountryCode(), null);
        $dhlSample->Consignee->Contact->PersonName = $consigneeName;
        $dhlSample->Consignee->Contact->PhoneNumber = $shipAddress->getPhoneNumber();

        $dhlSample->Commodity->CommodityCode = 'cc';

        $dhlSample->Dutiable->DeclaredValue = number_format(($order->getItemsTotal() /100), 2, '.', '');
        $dhlSample->Dutiable->DeclaredCurrency = $order->getCurrencyCode();
        $dhlSample->Reference->ReferenceID = $order->getId();

        $numP = $shipment->getDhlNumberOfPieces();
        $dhlSample->ShipmentDetails->NumberOfPieces = $numP;
        for ($i = 0; $i <= $shipment->getDhlNumberOfPieces(); $i++) {
            $piece = new Piece;
            $piece->PieceID = (string)($i + 1);
            $dhlSample->ShipmentDetails->addPiece($piece);
        }

        $dhlSample->ShipmentDetails->Weight = $shipment->getDhlWeight();
        $dhlSample->ShipmentDetails->WeightUnit = 'K';
        $dhlSample->ShipmentDetails->GlobalProductCode = 'P';
        $dhlSample->ShipmentDetails->Date = date('Y-m-d');
        $dhlSample->ShipmentDetails->Contents = 'Automotive parts';
        $dhlSample->ShipmentDetails->DimensionUnit = 'C';
        $dhlSample->ShipmentDetails->InsuredAmount = $shipment->getDhlInsuredAmount();

        $dhlSample->ShipmentDetails->CurrencyCode = $order->getCurrencyCode();
        $dhlSample->Shipper->ShipperID = $gatewayConfig['shipperAccountNumber'];
        $dhlSample->Shipper->CompanyName = $gatewayConfig['company'];
        $dhlSample->Shipper->addAddressLine($gatewayConfig['address']);
        $dhlSample->Shipper->City = $gatewayConfig['city'];
        $dhlSample->Shipper->PostalCode = $gatewayConfig['postalCode'];
        $dhlSample->Shipper->CountryCode = $gatewayConfig['countryCode'];
        $dhlSample->Shipper->CountryName = Intl::getRegionBundle()->getCountryName($gatewayConfig['countryCode'], null);
        $dhlSample->Shipper->Contact->PersonName = $gatewayConfig['name'];
        $dhlSample->Shipper->Contact->PhoneNumber = $gatewayConfig['phone'];

        $dhlSample->LabelImageFormat = 'PDF';

        $client = new WebserviceClient($gatewayConfig['mode']);
        $xml = $client->call($dhlSample);

        $response = new ShipmentResponse();
        $response->initFromXML($xml);

        $label = $response->LabelImage->OutputImage;
        $shipment->setTracking($response->AirwayBillNumber);

        $this->em->persist($shipment);
        $this->em->flush();

        return $label;
    }
}