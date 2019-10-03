<?php
/**
 * Created by PhpStorm.
 * User: m153
 * Date: 3/7/18
 * Time: 4:43 PM
 */

namespace AppBundle\Form\Listener;


use AppBundle\Entity\ShippingMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddShipmentOrderItemListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATAP => 'postSetData');
    }

    public function postSetData(FormEvent $event)
    {
        $orderItem = $event->getData();
        $form = $event->getForm();

        $warehouseShipmentMethod = $event->getForm()->getData()->getWarehouse()->getShippingMethod();
        $currentShipmentMethod = $event->getForm()->getData()->getUnits()->get(0)->getShipment()->getMethod()->getId();

        $warehouseShppmentMethod = $warehouseShipmentMethod->filter(function (ShippingMethod $shippingMethod) use ($currentShipmentMethod) {
            return $shippingMethod->getId() == $currentShipmentMethod;
        });

        if (!$warehouseShppmentMethod->count()) {

            $event->setData([
                "shipment" => 3
            ]);
        }
    }
}
