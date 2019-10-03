<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Taxon;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\TaxonRepository;
use AppBundle\Serializer\Normalizer\Menu\ShopMainMenuContainerNormalizer;
use AppBundle\Serializer\Normalizer\Menu\ShopMainMenuNormalizer;
use AppBundle\Serializer\Normalizer\Menu\ShopMainMenuProductsNormalizer;
use AppBundle\VinCheck\CompatibilityProducts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShopMainMenuController
 *
 * @package AppBundle\Controller\API
 */
class ShopMainMenuController extends Controller
{
    /**
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var ProductRepository;
     */
    private $productRepository;

    /**
     * ShopMainMenuController constructor.
     *
     * @param TaxonRepository $taxonRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(TaxonRepository $taxonRepository, ProductRepository $productRepository)
    {
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Show all menu items
     *
     * @return JsonResponse
     */
    public function index()
    {
        $menu = $this->taxonRepository->findBy(['level' => '1'], ['position' => 'ASC']);
        $data = (new ShopMainMenuNormalizer())->normalize($menu);
        return new JsonResponse($data);
    }

    /**
     * Show category by id
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function show(Request $request, $id)
    {
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        /** @var Taxon $taxon */
        if (!$taxon = $this->taxonRepository->find($id)) {
            return new JsonResponse('Menu not found', JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->getData($request, $taxon, $channel));
    }

    /***
     * Prepare menu response
     *
     * @param Request $request
     * @param Taxon $taxon
     * @param $channel
     * @return array
     */
    private function getData(Request $request, Taxon $taxon, $channel)
    {
        $products = $this->productRepository->getProductByChannelAndTaxon($channel, $taxon);

        $products = (new ShopMainMenuProductsNormalizer())->normalize($products, null, ['channel' => $channel]);

        if (!$vinData = $request->getSession()->get('vincheck')) {
            $containers = $this->taxonRepository->getTaxonContainers($taxon->getId());
            $containers = (new ShopMainMenuContainerNormalizer())->normalize($containers);
        } else {
            $array = [];

            /** @var Taxon $item */
            foreach ($taxon->getChildren() as $item) {

                /** @var CompatibilityProducts $helper */
                $helper = $this->get('app.vin_check.compatibility_products');

                foreach ($item->getProducts() as $product) {

                    if ($helper->checkCompatibilityProduct($product, $vinData) === 'Yes') {
                        $array[] = $product;
                    }
                }
            }
            $containers = (new ShopMainMenuProductsNormalizer())->normalize($array, null, ['channel' => $channel]);
        }
        $data = array_merge($containers, $products);
        return $data;
    }
}