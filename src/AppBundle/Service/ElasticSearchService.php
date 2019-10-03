<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Taxon;
use AppBundle\Repository\ChannelRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\TaxonRepository;
use AppBundle\Serializer\Normalizer\ProductContainresNormalizer;
use AppBundle\Serializer\Normalizer\ProductNormalizer;
use AppBundle\Serializer\Normalizer\RatingNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\Channel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ElasticSearchService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
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
     * @var Container
     */
    private $container;

    /**
     * ElasticSearchService constructor.
     *
     * @param EntityManagerInterface $em
     * @param TaxonRepository $taxonRepository
     * @param ChannelRepository $channelRepository
     * @param ProductRepository $productRepository
     * @param Container $container
     */
    public function __construct(
        EntityManagerInterface $em,
        TaxonRepository $taxonRepository,
        ChannelRepository $channelRepository,
        ProductRepository $productRepository,
        Container $container
    ) {
        $this->em = $em;
        $this->taxonRepository = $taxonRepository;
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->container = $container;
    }

    /**
     *  Get query for products array
     *
     * @param $query
     * @param $channel
     * @return array
     */
    public function getQuery($query, $channel)
    {
        $query = mb_strtolower($query);
        $query = trim($query);

        return [
            "query" => [
                "bool" => [
                    "should" => [
                        ["match" => ['name' => $query]],
                        ["match" => ['shortDescription' => $query]],
                        [
                            "nested" => [
                                "path" => "productDescriptions",
                                "query" => [
                                    "bool" => [
                                        "should" => [
                                            ["match" => ["productDescriptions.description" => $query]]
                                        ],
                                    ]
                                ],
                            ]
                        ],
                    ],
                    'minimum_should_match' => 1,
                    'filter' => [
                        ["term" => [
                            "enabled" => true
                        ]],
                        ["nested" => [
                            "path" => "channels",
                            "score_mode" => "avg",
                            "query" => [
                                "bool" => [
                                    "should" => [
                                        "match" => ["channels.code" => $channel],
                                    ],
                                ]
                            ]
                        ]],
                    ],

                ],

            ],
        ];
    }

    /**
     *  Get query for blog array
     *
     * @param $query
     * @param bool $must
     * @return array
     */
    public function getQueryForBlog($query, $must = false)
    {
        $query = mb_strtolower($query);

        return [
            "query" => [
                "bool" => [
                    "should" => [
                        ["match" => ['slug' => $query]],
                        ["match" => ['title' => $query]],
                        ["match" => ['content' => $query]],
                    ],
                    'minimum_should_match' => 1,
                    'filter' => [
                        ["term" => [
                            "enabled" => true
                        ]]
                    ]
                ],
            ],
        ];
    }

    /**
     * Get product price for channel
     *
     * @param array $productVariant
     * @param $channel
     * @return mixed
     */
    public function getPricingForChannel(array $productVariant, $channel)
    {
        /** @var ProductVariant $item */
        foreach ($productVariant as $item) {
            return $item->getChannelPricings()->get($channel)->getPrice();
        }
    }

    /**
     * Find product container
     *
     * @param $products
     * @return array
     */
    public function findContainers($products)
    {

        $taxonContainerProducts = [];
        $productKeyToTaxonId = [];
        foreach ($products as $key => $item) {
            /** @var  Product $item */
            /** @var Taxon $taxon */
            if ($taxon = $item->getMainTaxon()) {
                if ($taxon->getLevel() == Taxon::TAXON_CONTAINER) {
                    $productKeyToTaxonId[$key] = $taxon->getId();
                    $taxonContainerProducts[$taxon->getId()] = $taxon;
                }
            }
        }

        return [$taxonContainerProducts, $productKeyToTaxonId];
    }

    /**
     * @param $productResults
     * @param $productKeyToTaxonId
     * @param $taxonContainerProducts
     * @return array
     */
    public function regroupFilterResults($productResults, $productKeyToTaxonId, $taxonContainerProducts)
    {
        $addedTaxon = [];
        $regroupResults = [];
        foreach ($productResults as $key => $value) {
            /** @var Product $value */
            /** @var Taxon $taxon */
            if ($taxon = $value->getMainTaxon()) {
                if ($taxon->getLevel() == Taxon::TAXON_CONTAINER) {
                    foreach ($productKeyToTaxonId as $productKey => $taxonId) {
                        if ($key == $productKey && !in_array($taxonId, $addedTaxon)) {
                            $addedTaxon[] = $taxonId;
                            $regroupResults[$key] = $taxonContainerProducts[$taxonId];
                        }
                    }
                } else {
                    $regroupResults[$key] = $productResults[$key];
                }
            } else {
                $regroupResults[$key] = $productResults[$key];
            }
        }

        return $regroupResults;
    }

    /**
     * @param $regroupedProducts
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function elasticProductsCompatibility($regroupedProducts, Request $request)
    {
        $result = [];

        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        try {
            $channel = $this->container->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        } catch (\Exception $e) {
        }
        if (!$channel) {
            $channelCode = $request->cookies->get('_channel_code');
            $channelCode = ($channelCode) ? $channelCode : 'bimmer_tech';
            /** @var Channel $channel */
            $channel = $this->channelRepository->findOneByCode($channelCode);
        }
        try {
            $productReviewRepository = $this->container->get('sylius.repository.product_review');
        } catch (\Exception $e) {
        }

        foreach ($regroupedProducts as $key => $productWithContainer) {
            if ($productWithContainer instanceof Taxon) {
                if ($sessionProducts !== null) {
                    $taxonProducts = $productWithContainer->getProducts();

                    /** @var Product $product */
                    foreach ($taxonProducts as $product) {
                        $this->container->get('app.vin_check.compatibility_products')
                            ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
                        if ($product->getCompatibility() == ProductInterface::COMPATIBILITY_YES) {

                            $result['productContainers'][$key] = (new ProductNormalizer())
                                ->normalize($product, 'json', ['channel' => $channel, "image_size" => "product_list"]);

                            $rating = $productReviewRepository->starRatingByProduct($product->getId());

                            $result['productContainers'][$key] = array_merge(
                                $result['productContainers'][$key],
                                (new RatingNormalizer())->normalize($rating)
                            );
                        }
                    }
                }

                if ($sessionProducts !== null && !isset($result['productContainers'][$key])) {
                    $compatibility = $sessionProducts['compatibility'] == Product::COMPATIBILITY_NOT_SURE ?
                        Product::COMPATIBILITY_NOT_SURE : Product::COMPATIBILITY_NO;
                    $productWithContainer->setCompatibility($compatibility);
                }

                if (!isset($result['productContainers'][$key])) {
                    $taxonRating = $this->productRepository->taxonRating($productWithContainer->getId(), $channel);

                    $result['productContainers'][$key] = (new ProductContainresNormalizer())
                        ->normalize($productWithContainer, 'json', [
                            "image_size" => "product_list",
                            "rating" => $taxonRating,
                        ]);
                }
            } elseif ($productWithContainer instanceof Product) {
                if ($sessionProducts !== null) {
                    $compatibilityService = $this->container->get('app.vin_check.compatibility_products');
                    $compatibilityService->setOneCompatibility($productWithContainer, $sessionProducts['products'],
                        $sessionProducts['compatibility']);
                }

                $result['products'][$key] = (new ProductNormalizer())
                    ->normalize($productWithContainer, 'json', ['channel' => $channel, "image_size" => "product_list"]);

                $rating = $productReviewRepository->starRatingByProduct($productWithContainer->getId());
                $result['products'][$key] = array_merge(
                    $result['products'][$key],
                    (new RatingNormalizer())->normalize($rating)
                );
            }

        }

        return $result;
    }

    /**
     * Get product image path
     *
     * @param array $images
     * @return null|string
     */
    private function getImage(array $images)
    {
        if (!$images) {
            return null;
        }
        return $_SERVER['HTTP_HOST'] . '/media/image/' . $images[0]->getPath();
    }

    /**
     * @param $products
     * @param $channelCode
     * @param bool $showAll
     * @return array
     */
    public function serializeProducts($products, $channelCode, $showAll = false)
    {
        $count = !$showAll ? 4 : count($products);
        $counter = 0;
        $productsArray = [];
        foreach ($products as $key => $value) {
            if ($counter < $count && $value instanceof Product) {
                /** @var Product $value */
                $productsArray[] = [
                    'id' => $value->getId(),
                    'name' => $value->getName(),
                    'shortDescription' => $value->getShortDescription(),
                    'description' => $value->getProductDescriptions()->count() > 0 ?
                        $value->getProductDescriptions()->first()->getDescription() : "",
                    'image_path' => $this->getImage($value->getImages()->toArray()),
                    'pricing' => $this->getPricingForChannel($value->getVariants()->toArray(), $channelCode),
                    'url' => '/products-' . $value->getSlug(),
                    'taxon' => $value->getMainTaxon() ? $value->getMainTaxon()->getName() : '',
                    'taxon_url' => $value->getMainTaxon() ? '/category-' . $value->getMainTaxon()->getSlug() : ''
                ];
            } elseif ($key < $count && $value instanceof Taxon) {
                /** @var Taxon $value */
                $productsArray[] = [
                    'id' => $value->getId(),
                    'name' => $value->getName(),
                    'shortDescription' => $value->getShortDescription(),
                    'description' => $value->getDescription(),
                    'image_path' => $this->getImage($value->getImages()->toArray()),
                    'pricing' => $value->getPrice(),
                    'url' => '/category-' . $value->getSlug(),
                    'taxon' => $value->getParent()->getName(),
                    'taxon_url' => '/category-' . $value->getSlug()
                ];
            }
            $counter = $counter + 1;
        }

        return $productsArray;
    }
}
