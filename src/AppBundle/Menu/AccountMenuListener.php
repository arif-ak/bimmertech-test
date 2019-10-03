<?php

namespace AppBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AccountMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAccountMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->addChild('delivery', ['route' => 'app_tracking_delivery_controller_account'])
            ->setLabel('Tracking delivery')
            ->setLabelAttribute('icon', 'map');

        $menu
            ->addChild('remove', ['route' => 'app_account_deactivate'])
            ->setLabel('Delete Account')
            ->setLabelAttribute('icon', 'remove');

        $menu->removeChild('wishlists');
        $menu->getChild('order_history')->setLabel('Order');
    }
}
