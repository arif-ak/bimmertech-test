<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogProducts;
use AppBundle\Serializer\Normalizer\BlogPostNormalizer;
use AppBundle\Serializer\Normalizer\InterestedInNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @param SlugGeneratorInterface $slugGenerator
     */
    public function __construct(SlugGeneratorInterface $slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);
        $categoryRepository = $em->getRepository(BlogCategory::class);


        $latestPost = $repository->getLatest();
        $postCategories = $categoryRepository->findAll();
        $postsByCategoryArray = [];
        /** @var BlogCategory $category */
        foreach ($postCategories as $category) {
            if ($category->getEnabled()) {
                $id = $category->getId();
                $name = $category->getName();
                $postsOfCategory = $repository->findBy(['blogCategory' => $id]);

                $postsByCategoryArray[$name] = (new BlogPostNormalizer())->normalize($postsOfCategory);
            }
        }

        return new JsonResponse(
            ['data' =>
                [
                    'latest' => (new BlogPostNormalizer())->normalize($latestPost),
                    'postByCategory' => $postsByCategoryArray,
                ]
            ]);
    }

    /**
     * @return Response
     */
    public function mostReviewed()
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);

        /** @var BlogPost $post */
        $posts = $repository->getLatestMainPage();
        $results = (new BlogPostNormalizer())->normalize($posts);

        return new JsonResponse(['data' => $results]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function generateAction(Request $request): JsonResponse
    {
        $name = $request->query->get('name');

        return new JsonResponse([
            'slug' => $this->slugGenerator->generate($name),
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function interestingProducts(Request $request)
    {
        $em = $this->getDoctrine();
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $interestedIn = $em->getRepository(BlogProducts::class)->findAll();
        $blogProducts = (new ArrayCollection($interestedIn))->map(function ($blogProduct) {
            return $blogProduct->getProduct();
        })->toArray();

        $products = $this->get('app.service.interesting_in_compatible')
            ->getCompatible($blogProducts, $request->getSession());

        $products = (new ArrayCollection($products))->map(function ($product) use ($channel) {
            return (new InterestedInNormalizer)
                ->normalize($product, null, ['channel' => $channel]);
        })->getValues();

        return new JsonResponse($products);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function getInterestingInProducts(Request $request, $slug)
    {
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        if (!$post = $this->get('app.repository.blog_post')->findOneBySlug($slug)) {
            return new JsonResponse('Post not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $interestedIn = $post->getProductRelated() ? $post->getProductRelated()->getProducts()->toArray() : [];

        $products = $this->get('app.service.interesting_in_compatible')
            ->getCompatible($interestedIn, $request->getSession());

        $products = (new ArrayCollection($products))->map(function ($product) use ($channel) {
            return (new InterestedInNormalizer)
                ->normalize($product, null, ['channel' => $channel]);
        })->getValues();

        return new JsonResponse($products);
    }
}
