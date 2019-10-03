<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxon;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use AppBundle\Repository\TaxonRepository;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TaxonomyMenuController
 * @package AppBundle\Controller
 */
class TaxonomyMenuController extends Controller
{
    /**
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * TaxonomyMenuController constructor.
     *
     * @param TaxonRepository $taxonRepository
     * @param ChannelRepository $channelRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(TaxonRepository $taxonRepository, ChannelRepository $channelRepository, ProductRepository $productRepository)
    {
        $this->taxonRepository = $taxonRepository;
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get menu with taxons and product
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $menu = [];
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        if (!$channel) {
            $channelCode = $request->cookies->get('_channel_code');
            $channelCode = ($channelCode) ? $channelCode : 'bimmer_tech';
            /** @var Channel $channel */
            $channel = $this->channelRepository->findOneByCode($channelCode);
        }
        $taxonomy = $this->taxonRepository->orderByPosition();

        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        /** @var Taxon $item */
        foreach ($taxonomy as $item) {
            $products = $this->productRepository->createShopListQueryBuilder($channel, $item, 'en_US', ['name' => 'ASC'])
                ->getQuery()
                ->getResult();
            if ($sessionProducts !== null) {
                $compatibilityService = $this->get('app.vin_check.compatibility_products');
                $compatibilityService->setAllCompatibility($products, $sessionProducts['products'], $sessionProducts['compatibility']);
                if ($item->hasChildren()) {
                    /** @var Taxon $child */
                    foreach ($item->getChildren() as $child) {
                        if ($child->isContainer()) {
                            $containerProducts = $this->productRepository
                                ->createShopListQueryBuilder($channel, $child, 'en_US', ['name' => 'ASC'])
                                ->getQuery()
                                ->getResult();
                            $containerProd = $compatibilityService
                                ->compatibilityContainer($containerProducts, $sessionProducts['products'], $sessionProducts['compatibility']);
                            if ($containerProd) {
                                array_push($products, $containerProd);
                            }
                        }
                    }
                }
            }

            $menu[] = [
                'taxon' => $item,
                'products' => $products
            ];
        }

        return $this->render('@SyliusShop/Taxon/_horizontalMenu.html.twig', [
            'menu' => $menu
        ]);
    }
}