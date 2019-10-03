<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use AppBundle\Entity\BlogPostProduct;
use AppBundle\Entity\Product;
use AppBundle\Repository\BlogPostRepository;
use AppBundle\Serializer\Normalizer\AdminBlogPostNormalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogPostController
 *
 * @package AppBundle\Controller\API\Admin
 */
class BlogPostController extends Controller
{
    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    /**
     * @var BlogCategoryRepository;
     */
    private $blogCategoryRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * BlogPostController constructor.
     * @param EntityRepository $blogPostRepository
     * @param EntityRepository $blogCategoryRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        EntityRepository $blogPostRepository,
        EntityRepository $blogCategoryRepository,
        ObjectManager $objectManager
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @return JsonResponse
     */
    public function getBlogProducts(): JsonResponse
    {
        $products = $this->get('sylius.repository.product')->findBy(['enabled' => true]);
        $data = [];
        /** @var Product $item */
        foreach ($products as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'code' => $item->getCode()
            ];
        }

        return new JsonResponse($data);
    }

    /**
     *  Get Blog post
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getPost(Request $request, $id): JsonResponse
    {
        if (!$post = $this->blogPostRepository->find($id)) {
            return new JsonResponse('Post not found', JsonResponse::HTTP_NOT_FOUND);
        }
        $data = (new AdminBlogPostNormalizer())->normalize($post);
        return new JsonResponse($data);
    }

    /**
     * Create new blog post
     * @param Request $request
     * @return JsonResponse
     */
    public function newPost(Request $request): JsonResponse
    {
        $blogPost = new BlogPost();
        /** @var AdminUser $user */
        $user = $this->getUser();
        $request->request->set('author', $user->getFirstName() . " " . $user->getLastName());

        $data = $this->setData($request, $blogPost);

        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function editPost(Request $request, $id): JsonResponse
    {


        if (!$post = $this->blogPostRepository->find($id)) {
            return new JsonResponse('Post not found', JsonResponse::HTTP_NOT_FOUND);
        }
        $data = $this->setData($request, $post);
        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        if (!$categories = $this->blogCategoryRepository->findAll()) {
            return new JsonResponse('Post not found', JsonResponse::HTTP_NOT_FOUND);
        }
        $data = [];
        /** @var BlogCategory $item */
        foreach ($categories as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'enable' => $item->getEnabled()
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkSlug(Request $request): JsonResponse
    {
        $slug = $request->get('slug');
        /** @var BlogPost $post */
        $post = $this->blogPostRepository->findOneBySlug($slug);
        $data = [
            'slug' => $slug,
            'isExist' => boolval($post)
        ];
        return new JsonResponse($data);
    }

    /**
     * Set blog data;
     * @param Request $request
     * @param BlogPost $blogPost
     * @return array|bool|float|int|string
     */
    private function setData(Request $request, BlogPost $blogPost)
    {
        if ($category = $this->blogCategoryRepository->find($request->get('category', 0))) {
            $blogPost->setBlogCategory($category);
        }

        $blogPost->setTitle($request->get('title'));
        if ($request->get("author")) {
            $blogPost->setAuthor($request->get('author'));
        }

        $blogPost->setSlug($request->get('slug'));
        $blogPost->setEnabled($request->get('enabled'));
        $blogPost->setMetaTags($request->get('metaTags'));
        $blogPost->setMetaDescription($request->get('metaDescription'));
        $blogPost->setMetaKeywords($request->get('metaKeywords'));
        $blogPost->setContent($request->get('content'));
        $blogPost->setCreatedAt(new \DateTime('now'));
        $blogPost->setSeoText($request->get('seoText'));

//        $this->setProductsRelated($request, $blogPost);
        $this->setMainImage($request, $blogPost);
        $this->blogPostRepository->add($blogPost);

        return (new AdminBlogPostNormalizer())->normalize($blogPost);
    }

    /**
     * @param Request $request
     * @param BlogPost $blogPost
     */
    private function setProductsRelated(Request $request, BlogPost $blogPost): void
    {
        $ids = $request->get('relatedProducts', [0]);
        $products = $this->get('sylius.repository.product')->findByIds($ids);
        $blogPost->removeProductRelatedAll();
        foreach ($products as $item) {
            $blogPostProduct = new BlogPostProduct();
            $blogPostProduct->setProduct($item);
            $blogPost->addProductRelated($blogPostProduct);
        }
    }

    /**
     *  Set blog image
     * @param Request $request
     * @param BlogPost $blogPost
     */
    private function setMainImage(Request $request, BlogPost $blogPost): void
    {
        $thumbnail = $request->get('thumbnail');
        $blogPost->removeAllBlogPostImage();

        foreach ($thumbnail as $key => $value) {
            $ratio = $key == 'oneToOne' ? '1:1' : '2:1';
            $image = new BlogPostImage();
            $image->setPath($value);
            $image->setAspectRatio($ratio);
            $blogPost->addBlogPostImage($image);
        }
    }
}
