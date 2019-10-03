<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Product;
use AppBundle\Entity\Taxon;
use AppBundle\Entity\TaxonProductRelated;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\TaxonRepository;
use AppBundle\Serializer\Normalizer\CategoryNormalizer;
use AppBundle\Serializer\Normalizer\InterestedInNormalizer;
use AppBundle\Serializer\Normalizer\ProductContainresNormalizer;
use AppBundle\Serializer\Normalizer\ProductListNormalizer;
use AppBundle\Serializer\Normalizer\RatingNormalizer;
use AppBundle\VinCheck\CompatibilityProducts;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 *
 * @package AppBundle\Controller\API
 */
class CategoryController extends Controller
{
    /**
     * @var TaxonRepository
     */
    private $categoryRepository;

    /**
     * @var CompatibilityProducts
     */
    private $compatibilityProducts;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    private $result = [
        'productContainers' => [],
        'products' => [],
    ];

    /**
     * CategoryController constructor.
     *
     * @param TaxonRepository $categoryRepository
     * @param CompatibilityProducts $compatibilityProducts
     * @param ProductRepository $productRepository
     */
    public function __construct(TaxonRepository $categoryRepository, CompatibilityProducts $compatibilityProducts,
                                ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->compatibilityProducts = $compatibilityProducts;
        $this->productRepository = $productRepository;
    }

    public function getById($id)
    {
        /** @var Taxon $category */
        if (!$category = $this->categoryRepository->find($id)) {
            return new JsonResponse('Category not found', 404);
        }
        return new JsonResponse((new CategoryNormalizer())->normalize($category));
    }

    /**
     * Get product by slug
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function index(Request $request, $slug)
    {
        /** @var Taxon $category */
        if (!$category = $this->categoryRepository->findOneBySlug($slug, 'en_US')) {
            return new JsonResponse('Category not found', 404);
        }

        $this->getProducts($category, $request);
        $this->getContainers($category, $request);
        return new JsonResponse($this->result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function starRating(Request $request)
    {
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $data = $request->request->all();
        $slug = isset($data['slug']) ? $data['slug'] : null;

        /** @var Taxon $category */
        if (!$category = $this->categoryRepository->findOneBySlug($slug, 'en_US')) {
            return new JsonResponse('Category not found', 404);
        }

        /** @var Taxon $category */
        if ($category->getLevel() == Taxon::TAXON_CONTAINER) {
            $productRepository = $this->container->get('sylius.repository.product');
            $rating = $productRepository->taxonRating($category->getId(), $channel);

            $customerRating = (new RatingNormalizer)->normalize($rating);

            return new JsonResponse(['reviews' => $customerRating]);
        }
        return new JsonResponse('This is not container', 400);
    }

    /**
     * Get taxon properties by type
     *
     * @param Request $request
     * @param $id
     * @param $type
     * @return JsonResponse
     */
    public function taxonProperty(Request $request, $id, $type)
    {
        /** @var Taxon $category */
        if (!$category = $this->categoryRepository->find($id)) {
            return new JsonResponse('Category not found', 404);
        }

        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        if (class_exists($class = 'AppBundle\\Serializer\\Normalizer\\' . ucfirst($type) . 'Normalizer')) {
            $normalizer = (new $class())->normalize($category, 'json', ['channel' => $channel]);

            return new JsonResponse([$type => $normalizer], 200);
        }
        return new JsonResponse('Data not found', 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function interestingProducts(Request $request, $slug)
    {
        /** @var Taxon $category */
        if (!$category = $this->categoryRepository->findOneBySlug($slug, 'en_US')) {
            return new JsonResponse('Category not found', 404);
        }
        return new JsonResponse($this->getRelatedProducts($category, $request));
    }

    /**
     * @param Taxon $taxon
     * @param Request $request
     * @return array
     */
    private function getRelatedProducts(Taxon $taxon, Request $request)
    {
        $em = $this->getDoctrine();
        $products = [];
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $taxonProducts = $em->getRepository(TaxonProductRelated::class)->
        findOneBy(['category' => $taxon->getId()]);
        if ($taxonProducts) {
            $products = $taxonProducts->getProducts()->toArray();
        }

        $products = $this->get('app.service.interesting_in_compatible')
            ->getCompatible($products, $request->getSession());

        $products = (new ArrayCollection($products))->map(function ($product) use ($channel) {
            /** @var Product $product */
            if ($product->isEnabled()) {
                return (new InterestedInNormalizer)
                    ->normalize($product, null, ['channel' => $channel]);
            }
        })->getValues();

        return $products;
    }

    /**
     * @param Taxon $category
     * @param Request $request
     */
    private
    function getContainers(Taxon $category, Request $request)
    {

        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        /** @var ArrayCollection $containers */
        $containers = $category->getChildren();

        if (!$vinCheck = $request->getSession()->get('vincheck')) {
            $this->result['productContainers'] = $containers->map(function ($container) use ($channel) {
                $taxonRating = $this->productRepository->taxonRating($container->getId(), $channel);
                return (new ProductContainresNormalizer())->normalize($container, 'json', [
                    "image_size" => "product_list",
                    "rating" => $taxonRating,
                ]);

            })->getValues();
        } else {

            /** @var Taxon $container */
            foreach ($containers as $container) {
                $compateble = 'No';
                $products = $this->productRepository->productPagination($container->getId(), $channel);

                foreach ($products as $product) {
                    $compatibility = $this->compatibilityProducts->checkCompatibilityProduct($product[0], $vinCheck);
                    if ($compatibility !== 'No') {

                        $product[0]->setCompatibility($compatibility);
                        $product = (new ProductListNormalizer())
                            ->normalize($product, 'json', ['channel' => $channel, "image_size" => "product_list"]);
                        array_unshift($this->result['products'], $product);
                        $compateble = $compatibility;
                    }
                }

                if ($compateble === 'No') {
                    $taxonRating = $this->productRepository->taxonRating($container->getId(), $channel);
                    $container->setCompatibility($compateble);
                    $this->result['products'][] =
                        (new ProductContainresNormalizer())->normalize($container, 'json', [
                            "image_size" => "product_list",
                            "rating" => $taxonRating,
                        ]);
                }
            }
        }
    }

    /**
     * Get Category's products
     * @param Taxon $category
     * @param Request $request
     */
    private
    function getProducts(Taxon $category, Request $request)
    {
        $vinCheck = $request->getSession()->get('vincheck');
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $page = $request->get('page') ?: 0;

        $products = $this->productRepository->productPagination($category->getId(), $channel, $page);
        /** @var ArrayCollection $products */
        $products = (new ArrayCollection($products))->map(function ($product) use ($vinCheck) {
            $compatibility = null;

            if ($vinCheck) {
                $compatibility = $this->compatibilityProducts->checkCompatibilityProduct($product[0], $vinCheck);
            }
            /** @var Product $product */
            $product[0]->setCompatibility($compatibility);
            return $product;

        });
        $products = $this->sort($products);

        $this->result['products'] = $products->map(function ($product) use ($channel) {

            return (new ProductListNormalizer())
                ->normalize($product, 'json', ['channel' => $channel, "image_size" => "product_list"]);
        })->getValues();

    }

    /**
     * @param $collection
     * @return mixed
     */
    private
    function sort($collection)
    {
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a[0]->getCompatibility() > $b[0]->getCompatibility()) ? -1 : 1;
        });
        $amenitiesSorted = new ArrayCollection(iterator_to_array($iterator));

        return $amenitiesSorted;
    }

}
