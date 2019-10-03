<?php

namespace AppBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ShippingExportMenuBuilder
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $autChecker;

    /**
     * AdminMenuListener constructor.
     */
    public function __construct(AuthorizationCheckerInterface $autChecker)
    {
        $this->autChecker = $autChecker;
    }

    /**
     * @param MenuBuilderEvent $event
     */
    public function buildMenu(MenuBuilderEvent $event): void
    {
//        $event
//            ->getMenu()
//            ->getChild('sales')
//            ->addChild('shipping_exports', ['route' => 'bitbag_admin_shipping_export_index'])
//            ->setName('bitbag.ui.export_shipments')
//            ->setLabelAttribute('icon', 'arrow up')
//        ;
    }
}
