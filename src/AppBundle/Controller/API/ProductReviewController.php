<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductReview;
use AppBundle\Entity\UserMarketing;
use AppBundle\Repository\ProductRepository;
use AppBundle\Serializer\Normalizer\ReviewsNormalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\User\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ProductReviewController
 * @package AppBundle\Controller\API
 */
class ProductReviewController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ProductReviewController constructor.
     *
     * @param ProductRepository $productRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(ProductRepository $productRepository, ObjectManager $objectManager)
    {
        $this->productRepository = $productRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->request->get('page');
        $productId = $request->request->get('object_id');

        if ($page >= 0 && $productId) {
            $productReviewRepository = $this->container->get('sylius.repository.product_review');
            $reviews = $productReviewRepository->pagination($productId, $page);

            $productReviews = (new ReviewsNormalizer())->normalize($reviews);

            return new JsonResponse($productReviews);
        }

        throw new BadRequestHttpException('parameters not founded');
    }

    /**
     * Create product Review
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function create(Request $request, $id)
    {
        /** @var User $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('Please login', 401);
        }

        /** @var Product $product */
        if (!$product = $this->productRepository->find($id)) {
            return new JsonResponse('Product not found', 404);
        }

        $rating = $request->request->get('rating');
        $title = $request->request->get('title');
        $comment = $request->request->get('comment');

        if (!$rating || !$title || !$comment) {
            return new JsonResponse('Bad parameters rating or title or comment ', 404);
        }

        $roles = [UserMarketing::MARKETING_ROLE,AdminUser::DEFAULT_ADMIN_ROLE];
        $data = [
            'product' => $product,
            'user' => $user,
            'comment' => $comment,
        ];
        $this->get('app.service.admin_user')->sendEmail($roles, 'app_product_review_created', $data);

        $productReview = new ProductReview();
        $productReview->setAuthor($user->getCustomer());
        $productReview->setRating($rating);
        $productReview->setTitle($title);
        $productReview->setComment($comment);
        $productReview->setReviewSubject($product);
        $productReview->setStatus('new');
        $productReview->setCreatedAt(new \DateTime('now'));

        $this->objectManager->persist($productReview);
        $this->objectManager->flush();

        return new JsonResponse('Review added', 201);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function starRating($id)
    {
        $productReviewRepository = $this->container->get('sylius.repository.product_review');
        $rating = $productReviewRepository->starRatingByProduct($id);

        $customerRating = [
            'ratings' => [
                (int)$rating[0]['rating_5'],
                (int)$rating[0]['rating_4'],
                (int)$rating[0]['rating_3'],
                (int)$rating[0]['rating_2'],
                (int)$rating[0]['rating_1'],
            ],
            'averageRating' => $rating[0]['avg_rating']

        ];

        return new JsonResponse(['reviews' => $customerRating]);
    }
}
