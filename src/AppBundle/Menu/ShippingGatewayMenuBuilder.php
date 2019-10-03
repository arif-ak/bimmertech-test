<?php

namespace AppBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class ShippingGatewayMenuBuilder
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function buildMenu(MenuBuilderEvent $event): void
    {
//        $menu = $event->getMenu();
//        $configuration = $menu->getChild('configuration');
//        if ($configuration) {
//            $configuration->addChild('shipping_gateways', ['route' => 'bitbag_admin_shipping_gateway_index'])
//                ->setLabel('bitbag.ui.shipping_gateways')
//                ->setLabelAttribute('icon', 'cloud');
//        }
    }
}
