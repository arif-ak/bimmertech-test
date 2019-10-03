<?php

namespace AppBundle\Controller;

use AppBundle\Serializer\Normalizer\BlogPostNormalizer;
use AppBundle\Serializer\Normalizer\ElasticSearch\BlogPostNormalizer as PostNormalizer;
use AppBundle\Service\ElasticSearchService;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SearchElasticController
 * @package AppBundle\Controller
 */
class SearchElasticController extends Controller
{
    /**
     * @var ElasticSearchService
     */
    private $elasticSearchService;
    /**
     * @var PostNormalizer
     */
    private $blogPostNormalizer;

    /**
     * TaxonomyMenuController constructor.
     *
     * @param ElasticSearchService $elasticSearchService
     * @param PostNormalizer $blogPostNormalizer
     */
    public function __construct(
        ElasticSearchService $elasticSearchService,
        PostNormalizer $blogPostNormalizer
    ) {
        $this->elasticSearchService = $elasticSearchService;
        $this->blogPostNormalizer = $blogPostNormalizer;
    }

    /**
     * Search elastic products for ajax load
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function search(Request $request)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $channelCode = $channel->getCode();

        if (!$query = $request->get('q')) {
            return new JsonResponse('bad parameter ', 400);
        }

        if (!$channelCode) {
            return new JsonResponse('bad parameter channel', 400);
        }

        $finder = $this->container->get('fos_elastica.finder.app.product');
        $finderBlogPost = $this->container->get('fos_elastica.finder.app_post.blog_post');
        $postsResult = $finderBlogPost->find($this->elasticSearchService->getQueryForBlog($query, $must = true));
        $productResults = $finder->find($this->elasticSearchService->getQuery($query, $channelCode));

        list($taxonContainerProducts, $productKeyToTaxonId) = $this->elasticSearchService->
            findContainers($productResults);
        $regroupResults = $this->elasticSearchService->
            regroupFilterResults($productResults, $productKeyToTaxonId, $taxonContainerProducts);

        $postsArray = $this->blogPostNormalizer->normalize($postsResult, null, ['showAll' => false]);
        $productsArray = $this->elasticSearchService->serializeProducts($regroupResults, $channelCode);

        $products = ['products' => $productsArray];
        $posts = ['posts' => $postsArray];

        $result = array_merge($products, $posts);

        return new JsonResponse(['items' => $result], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function all(Request $request)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $channelCode = $channel->getCode();

        if (!$query = $request->get('q')) {
            return new JsonResponse('bad parameter', 400);
        }

        if (!$channelCode) {
            return new JsonResponse('bad parameter channel', 400);
        }

        $finder = $this->container->get('fos_elastica.finder.app.product');
        $finderBlogPost = $this->container->get('fos_elastica.finder.app_post.blog_post');
        $postsResult = $finderBlogPost->find($this->elasticSearchService->getQueryForBlog($query, true));
        $productResults = $finder->find($this->elasticSearchService->getQuery($query, $channelCode));

        list($taxonContainerProducts, $productKeyToTaxonId) = $this->elasticSearchService->
            findContainers($productResults);
        $regroupResults = $this->elasticSearchService->
            regroupFilterResults($productResults, $productKeyToTaxonId, $taxonContainerProducts);

        $productsArray = $this->elasticSearchService->elasticProductsCompatibility($regroupResults, $request);
        $postsArray = (new BlogPostNormalizer)->normalize($postsResult);

        $products = ['products' => $productsArray];
        $posts = ['posts' => $postsArray];

        $result = array_merge($products, $posts);

        return new JsonResponse(['items' => $result], 200);
    }

    /**
     * Search elastic products
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function searchProductPaginate(Request $request)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $channelCode = $channel->getCode();

        if (!$channelCode) {
            return new JsonResponse('bad parameter channel', 400);
        }

        if (!$query = $request->get('q')) {
            return new JsonResponse('bad parameter ', 400);
        }

        return $this->render('Search/index.html.twig', [
            'query' => $query
        ]);
    }
}
