<?php

namespace AppBundle\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class AdminProductFormMenuListener
{
    /**
     * @param ProductMenuBuilderEvent $event
     */
    public function addItems(ProductMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

//        $menu->removeChild('details');
////        $menu->removeChild('taxonomy');
//        $menu->removeChild('attributes');
//        $menu->removeChild('associations');
//        $menu->removeChild('media');

//        $menu
//            ->addChild('details')
//            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_details.html.twig')
//            ->setLabel('sylius.ui.details')
//            ->setCurrent(true);

//        $menu
//            ->addChild('description')
//            ->setAttribute('template', '@App/Admin/Product/Tab/_description.html.twig')
//            ->setLabel('Description')
//            ->setCurrent(true);

//        $menu
//            ->addChild('taxonomy')
//            ->setAttribute('template', '@App/Admin/Product/Tab/_taxonomy.html.twig')
//            ->setLabel('sylius.ui.taxonomy');

//        $menu
//            ->addChild('attributes')
//            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_attributes.html.twig')
//            ->setLabel('sylius.ui.attributes');
//
//        $menu
//            ->addChild('associations')
//            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_associations.html.twig')
//            ->setLabel('sylius.ui.associations');
//
//        $menu
//            ->addChild('media')
//            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_media.html.twig')
//            ->setLabel('sylius.ui.media');

        $menu
            ->addChild('description')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_description.html.twig')
            ->setLabel('Description')
            ->setCurrent(true);
        $menu
            ->addChild('installers')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_installers.html.twig')
            ->setLabel('Installation');

        $menu
            ->addChild('properties')
            ->setAttribute('template', '@App/Admin/Product/Tab/_property.html.twig')
            ->setLabel('Properties');
        $menu
            ->addChild('interestedInProduct')
            ->setAttribute('template', '@App/Admin/Product/Tab/_interestedIn.html.twig')
            ->setLabel('Interested In');
        $menu
            ->addChild('bestseller')
            ->setAttribute('template', '@App/Admin/Product/Tab/_bestseller.html.twig')
            ->setLabel('Flag');
        $menu
            ->addChild('buyersGuide')
            ->setAttribute('template', '@App/Admin/Product/Tab/_buyersGuide.html.twig')
            ->setLabel('Buyers guide');
        $menu
            ->addChild('popupOption')
            ->setAttribute('template', '@App/Admin/Product/Tab/_popupOption.html.twig')
            ->setLabel('Pop-ups');
        $menu
            ->addChild('dropDown')
            ->setAttribute('template', '@App/Admin/Product/Tab/_dropDown.html.twig')
            ->setLabel('Drop-downs');
        $menu
            ->addChild('savePrice')
            ->setAttribute('template', '@App/Admin/Product/Tab/_savePrice.html.twig')
            ->setLabel('Discount');
    }
}
