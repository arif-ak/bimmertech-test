<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Application\Kernel;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle,
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle,
            new \FOS\OAuthServerBundle\FOSOAuthServerBundle, // Required by SyliusAdminApiBundle.
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle,
            new \BitBag\SyliusMailChimpPlugin\BitBagSyliusMailChimpPlugin,
            new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle,
            new \SimpleBus\SymfonyBridge\SimpleBusEventBusBundle,
            new FOS\ElasticaBundle\FOSElasticaBundle,
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle,
            new \BitBag\SyliusShippingExportPlugin\BitBagSyliusShippingExportPlugin,
            new \BitBag\Dhl24PlShippingExportPlugin\Dhl24PlShippingExportPlugin,
            new \Webburza\Sylius\WishlistBundle\WebburzaSyliusWishlistBundle,
            new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle,
            new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle,
            new Ivory\CKEditorBundle\IvoryCKEditorBundle,
            new \AppBundle\AppBundle,
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new JMS\JobQueueBundle\JMSJobQueueBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }
}
