<?php

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Home;
use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Serializer\Normalizer\InterestedInNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class HomepageController extends Controller
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * HomepageController constructor.
     *
     * @param EngineInterface $templatingEngine
     * @param ProductRepository $productRepository
     */
    public function __construct(EngineInterface $templatingEngine, ProductRepository $productRepository)
    {
        $this->templatingEngine = $templatingEngine;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $home = $em->getRepository(Home::class)->findBy([], ['id' => 'DESC'], 1);

        return $this->templatingEngine->renderResponse('@SyliusShop/Homepage/index.html.twig', [
            'title' => count($home) > 0 ? $home[0]->getTitle() : "Bimmer-tech Web Shop",
            'subTitle' => count($home) > 0 ? $home[0]->getSubTitle() : "Bimmer-tech Web Shop",
            'metaTitle' => count($home) > 0 ? $home[0]->getMetaTitle() : "Bimmer-tech Web Shop",
            'metaDescription' => count($home) > 0 ? $home[0]->getMetaDescription() : null,
            'metaKeywords' => count($home) > 0 ? $home[0]->getMetaKeywords() : null,
            'description' => count($home) > 0 ? $home[0]->getDescription() : null,
            'interestingProducts' => $this->getRecommendedProducts($request),
        ]);
    }

    /**
     * Home page interesting in products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function interestingInProduct(Request $request)
    {
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $data = array_map(function ($product) use ($channel) {
            /** @var Product $product */
            return (new InterestedInNormalizer)->normalize($product, null, ['channel' => $channel]);
        }, $this->getRecommendedProducts($request));


        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getRecommendedProducts(Request $request)
    {
        if ($products = $this->productRepository->findBy([
            'recomended' => true,
            'enabled' => true
        ])) {
            return $this->get('app.service.interesting_in_compatible')->getCompatible($products, $request->getSession());
        }
        return [];
    }
}
