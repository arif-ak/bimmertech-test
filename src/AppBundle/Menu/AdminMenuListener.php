<?php

namespace AppBundle\Menu;

use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserMarketing;
use AppBundle\Entity\UserSale;
use AppBundle\Entity\UserSupport;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\AdminUser;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AdminMenuListener
 *
 * @package AppBundle\Menu
 */
final class AdminMenuListener
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
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $configurationMenu = $menu->getChild('configuration');

        $orders = $menu->addChild('Sales');
        $orders->removeChild('orders');

        $sales = $menu
            ->addChild('sales')
            ->setLabel('sylius.menu.admin.main.sales.header');

        if (
            $this->autChecker->isGranted(AdminUser::DEFAULT_ADMIN_ROLE) or
            $this->autChecker->isGranted(UserSale::SALE_ROLE) or
            $this->autChecker->isGranted(UserMarketing::MARKETING_ROLE) ) {

            $sales
                ->addChild('orders', ['route' => 'sylius_admin_order_index'])
                ->setLabel('Orders')
                ->setLabelAttribute('icon', 'cart');
        } else {

            $sales
                ->addChild('orders', ['route' => 'sylius_admin_role_order_index'])
                ->setLabel('Orders')
                ->setLabelAttribute('icon', 'cart');
        }


        $catalogMenu = $menu->getChild('catalog');
        $catalogMenu->addChild('DropDown', ['route' => 'app_admin_drop_down_index'])
            ->setLabel('DropDown');
        $catalogMenu->addChild('taxon_product_reconended', [
            'route' => 'app_admin_interested_in_category_index',

        ])
            ->setLabel('Interested In for categories')
            ->setLabelAttribute('icon', 'university');
        $page = $menu
            ->addChild('page')
            ->setLabel('Page');

        $page->addChild('page', ['route' => 'app_admin_custom_page_index'])
            ->setLabel('Page')
            ->setLabelAttribute('icon', 'reddit alien');
        $page->addChild('Help', ['route' => 'app_admin_help_question_index'])
            ->setLabel('Help')
            ->setLabelAttribute('icon', 'help');
        $page->addChild('Home', ['route' => 'app_admin_home_index'])
            ->setLabel('Home')
            ->setLabelAttribute('icon', 'home');


        $slider = $menu
            ->addChild('slider')
            ->setLabel('Slider');

        $slider
            ->addChild('slide', ['route' => 'app_admin_slide_index'])
            ->setLabel('Slide')
            ->setLabelAttribute('icon', 'reddit alien');
        $slider
            ->addChild('slider', ['route' => 'app_admin_slider_index'])
            ->setLabel('Slider')
            ->setLabelAttribute('icon', 'reddit alien');

        $rolesMenu = $menu
            ->addChild('roles')
            ->setLabel('Roles');
        $rolesMenu
            ->addChild('user_sale', ['route' => 'app_admin_user_sale_index'])
            ->setLabel('Sale')
            ->setLabelAttribute('icon', 'reddit alien');
        $rolesMenu
            ->addChild('user_logistic', ['route' => 'app_admin_user_logistic_index'])
            ->setLabel('Logistic')
            ->setLabelAttribute('icon', 'reddit alien');
        $rolesMenu
            ->addChild('user_support', ['route' => 'app_admin_user_support_index'])
            ->setLabel('Support')
            ->setLabelAttribute('icon', 'reddit alien');
        $rolesMenu
            ->addChild('user_coding', ['route' => 'app_admin_user_coding_index'])
            ->setLabel('Coding')
            ->setLabelAttribute('icon', 'reddit alien');
        $rolesMenu
            ->addChild('user_marketing', ['route' => 'app_admin_user_marketing_index'])
            ->setLabel('Marketing')
            ->setLabelAttribute('icon', 'reddit alien');
        $configurationMenu
            ->addChild('warehouse', ['route' => 'app_admin_warehouse_index'])
            ->setLabel('Warehouse')
            ->setLabelAttribute('icon', 'university');
        $configurationMenu
            ->addChild('buyers_guide_option', ['route' => 'app_admin_buyers_guide_option_index'])
            ->setLabel('Buyers guide option')
            ->setLabelAttribute('icon', 'random');
        $configurationMenu
            ->addChild('pop_up_option', ['route' => 'app_admin_pop_up_option_index'])
            ->setLabel('Pop up option')
            ->setLabelAttribute('icon', 'redo');

        $configurationMenu->addChild('Product Property', ['route' => 'app_admin_product_property_index'])
            ->setLabel('Product Property');
        $configurationMenu
            ->addChild('setting', ['route' => 'app_admin_general_option_index'])
            ->setLabel('Setting')
            ->setLabelAttribute('icon', 'setting');
        $configurationMenu
            ->addChild('color', ['route' => 'app_admin_color_index'])
            ->setLabel('Flag color')
            ->setLabelAttribute('icon', 'setting');

        $trackingNumber = $menu
            ->addChild('tracking_delivery')
            ->setLabel('Tracking delivery');
        $trackingNumber
            ->addChild('tracking', ['route' => 'app_tracking_delivery_controller_admin'])
            ->setLabel('Tracking')
            ->setLabelAttribute('icon', 'university');

        // contact page
        $contactMenu = $menu
            ->addChild('contact')
            ->setLabel('Contact');
        $contactMenu
            ->addChild('contact', ['route' => 'app_admin_contact_index'])
            ->setLabel('Contact')
            ->setLabelAttribute('icon', 'reddit alien');
        $contactMenu
            ->addChild('contact_title', ['route' => 'app_admin_contact_title_index'])
            ->setLabel('Contact title')
            ->setLabelAttribute('icon', 'reddit alien');

        // installer page
        $installer = $menu
            ->addChild('installer')
            ->setLabel('Installer');
        $installer->addChild('installer', ['route' => 'app_admin_installer_index'])
            ->setLabel('Installer')
            ->setLabelAttribute('icon', 'reddit item');

        // dealer page
        $dealer = $menu
            ->addChild('dealer')
            ->setLabel('Dealer');
        $dealer->addChild('dealer', ['route' => 'app_admin_dealer_index'])
            ->setLabel('Dealer')
            ->setLabelAttribute('icon', 'reddit item');

        // blog page
        $blog = $menu
            ->addChild('blog')
            ->setLabel('Blog');
        $blog->addChild('blog_category', ['route' => 'app_admin_blog_category_index'])
            ->setLabel('Blog Category')
            ->setLabelAttribute('icon', 'reddit item');
        $blog->addChild('blog_post', ['route' => 'app_admin_blog_post_index'])
            ->setLabel('Blog Post')
            ->setLabelAttribute('icon', 'reddit item');
        $blog->addChild('blog_review', ['route' => 'app_admin_blog_post_review_index'])
            ->setLabel('Blog review')
            ->setLabelAttribute('icon', 'reddit item');
        $blog->addChild('blog_products', ['route' => 'app_admin_blog_products_index'])
            ->setLabel('Blog interested in')
            ->setLabelAttribute('icon', 'reddit item');
        $blog->addChild('blog_post_products', ['route' => 'app_admin_blog_post_products_index'])
            ->setLabel('Blog post interested in')
            ->setLabelAttribute('icon', 'reddit item');
        // blog page
        $blog = $menu
            ->addChild('media_lidrary')
            ->setLabel('Media Library');
        $blog->addChild('blog_post', ['route' => 'app_admin_media_library_image'])
            ->setLabel('Image library');
        // info page
        $info = $menu
            ->addChild('info')
            ->setLabel('System info');
        $info->addChild('system_info', ['route' => 'app_system_info'])
            ->setLabel('System info')
            ->setLabelAttribute('icon', 'info');
        $info->addChild('server_logs', ['route' => 'app_server_logs'])
            ->setLabel('Server logs')
            ->setLabelAttribute('icon', 'exclamation');


        if ($this->autChecker->isGranted(UserSale::SALE_ROLE) ||
            $this->autChecker->isGranted(UserLogistic::LOGISTIC_ROLE) ||
            $this->autChecker->isGranted(UserSupport::SUPPORT_ROLE) ||
            $this->autChecker->isGranted(UserCoding::CODING_ROLE)
        ) {
            // leave item in menu
            $parentMenus = ['sales'];
            $this->leaveParentMenu($parentMenus, $event);
        }


        if ($this->autChecker->isGranted(UserMarketing::MARKETING_ROLE)) {
            // remove parent items
            $parentMenus = ['catalog', 'sales', 'customers', "marketing", "configuration", "page", "slider", "contact",
                "installer", "dealer", "blog", "media_lidrary"];
            $this->leaveParentMenu($parentMenus, $event);
        }

       
    }

    public function leaveChildrenMenuItems($parentMenu, array $childMenu, MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();
        $parentMenuObj = $menu->getChild($parentMenu);

        foreach ($parentMenuObj->getChildren() as $key => $child) {
            if (!in_array($key, $childMenu)) {
                $parentMenuObj->removeChild($key);
            }
        }
    }

    public function removeChildrenMenuItems($parentMenu, array $childMenu, MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();
        /** @var  $parentMenuObj */
        if ($parentMenuObj = $menu->getChild($parentMenu)) {
            foreach ($parentMenuObj->getChildren() as $key => $child) {
                if (in_array($key, $childMenu)) {
                    $parentMenuObj->removeChild($key);
                }
            }
        }
    }

    public function removeParentMenu(array $parentMenu, MenuBuilderEvent $event)
    {
        if ($menu = $event->getMenu()) {
            foreach ($menu->getChildren() as $key => $value) {
                if (in_array($key, $parentMenu)) {
                    $menu->removeChild($key);
                }
            }
        }
    }

    public function leaveParentMenu(array $parentMenu, MenuBuilderEvent $event)
    {
        if ($menu = $event->getMenu()) {
            foreach ($menu->getChildren() as $key => $value) {
                if (!in_array($key, $parentMenu)) {
//                    $menu->setDisplay(false);
                    $menu->removeChild($key);
                }
            }
        }
    }


    public function removeAdminMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        if ($catalogMenu = $menu->getChild('catalog')) {
            $catalogMenu->removeChild('inventory')
                ->removeChild('options');
        }

        if ($configuration = $menu->getChild('configuration')) {
            $configuration
                ->removeChild('zones')
                ->removeChild('currencies')
                ->removeChild('exchange_rates')
                ->removeChild('locales')
                ->removeChild('shipping_categories')
                ->removeChild('tax_categories')
                ->removeChild('tax_rates')

            ;
        }


    }
}
