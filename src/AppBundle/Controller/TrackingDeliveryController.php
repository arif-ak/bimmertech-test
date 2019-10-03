<?php

namespace AppBundle\Controller;

use AfterShip\AfterShipException;
use AfterShip\Trackings;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShippingMethodInterface;
use AppBundle\Form\Type\TrackingDeliveryType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackingDeliveryController
 *api2/user/orders
 * @package AppBundle\Controller
 */
class TrackingDeliveryController extends ResourceController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param EngineInterface $templatingEngine
     */
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AfterShipException
     */
    public function trackingDeliveryAdmin(Request $request)
    {
        $form = $this->createForm(TrackingDeliveryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $code = isset($data['code']) ? $data['code'] : "";
            $couriers = isset($data['couriers']) ? $data['couriers'] : null;

            try {
                $tracking = new Trackings($this->getParameter('after_ship_key'));
                // find tracking by code
                $response = $tracking->get($couriers, $code);
                $data = isset($response['data']) ? $response['data'] : null;
                $checkpoints = isset($data['tracking']) ? $data['tracking']['checkpoints'] : null;
            } catch (AfterShipException $e) {
                if ($e->getCode() == ShippingMethodInterface::SHIPMENT_NOT_EXIST) {
                    $shipments = $this->getDoctrine()->getRepository(Shipment::class)->
                    findBy(['tracking' => $code]);
                    /** @var Shipment $shipment */
                    $shipment = count($shipments) > 0 ? $shipments[0] : null;
                    if ($shipment && $shipment->getState() == Shipment::STATE_DELIVERED) {
                        $format = "Shipment with tracking number %s was successfully delivered";
                        $message = sprintf($format, $code);
                    } else {
                        try {
                            // create tracking in a system of After ship
                            $tracking->create($code, ['slug' => $couriers]);

                            // wait 2 sec
                            sleep(2);

                            // wait 2 sec
                            sleep(1);

                            // find tracking by code
                            $response = $tracking->get($couriers, $code);
                            $data = isset($response['data']) ? $response['data'] : null;
                            $checkpoints = isset($data['tracking']) ? $data['tracking']['checkpoints'] : null;

                            // delete tracking in a system of After ship
                            $delete = $tracking->delete($couriers, $code);
                        } catch (AfterShipException $e) {
                            $format = "Shipment with tracking number %s was not delivered";
                            $message = sprintf($format, $code);
                        }
                    }
                }

                return $this->templatingEngine->renderResponse(":TrackingDelivery:tracking_delivery.html.twig", [
                    'response' => $data ?? null,
                    'checkpoints' => $checkpoints ?? null,
                    'form' => $form->createView(),
                    'message' => isset($message) ? $message : null
                ]);
            }
        }
        return $this->templatingEngine->renderResponse(":TrackingDelivery:tracking_delivery.html.twig", [
            'response' => $data ?? null,
            'checkpoints' => $checkpoints ?? null,
            'form' => $form->createView(),
            'message' => isset($message) ? $message : null
        ]);
    }
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AfterShipException
     */
    public function trackingDeliveryAccount(Request $request)
    {
        $trackingNumber = $request->get('trackingNumber');
        $couriers = $request->get('couriers');
        if (!empty($trackingNumber) && $couriers) {
            try {
                $tracking = new Trackings($this->getParameter('after_ship_key'));

                // find tracking by code
                $response = $tracking->get($couriers, $trackingNumber);
                $data = isset($response['data']) ? $response['data'] : null;
                $checkpoints = isset($data['tracking']) ? $data['tracking']['checkpoints'] : null;
            } catch (AfterShipException $e) {
                if ($e->getCode() == ShippingMethodInterface::SHIPMENT_NOT_EXIST) {
                    $shipments = $this->getDoctrine()->getRepository(Shipment::class)->
                    findBy(['tracking' => $trackingNumber]);
                    /** @var Shipment $shipment */
                    $shipment = count($shipments) > 0 ? $shipments[0] : null;
                    if ($shipment && $shipment->getState() == Shipment::STATE_DELIVERED) {
                        $format = "Shipment with tracking number %s was successfully delivered";
                        $message = sprintf($format, $trackingNumber);
                    } else {
                        $format = "Shipment with tracking number %s was not delivered";
                        $message = sprintf($format, $trackingNumber);
                    }
                }
            }

            return $this->templatingEngine->renderResponse(":TrackingDelivery:tracking_delivery_account.html.twig", [
                'response' => $data ?? null,
                'trackingNumber' => $trackingNumber,
                'checkpoints' => $checkpoints ?? null,
                'message' => isset($message) ? $message : null
            ]);
        }

        return $this->templatingEngine->renderResponse(":TrackingDelivery:tracking_delivery_account.html.twig", [
            'response' => $data ?? null,
            'trackingNumber' => $trackingNumber ? $trackingNumber : "n/a",
            'checkpoints' => $checkpoints ?? null,
            'message' => isset($message) ?? null
        ]);
    }
}
