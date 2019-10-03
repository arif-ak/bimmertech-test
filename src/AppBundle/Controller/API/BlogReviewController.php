<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogReview;
use AppBundle\Entity\UserMarketing;
use AppBundle\Repository\BlogPostRepository;
use AppBundle\Repository\BlogReviewRepository;
use AppBundle\Serializer\Normalizer\ReviewsNormalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\User\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BlogReviewController
 * @package AppBundle\Controller\API
 */
class BlogReviewController extends Controller
{
    private $em;

    /**
     * @var BlogPostRepository
     */
    private $postRepository;

    /**
     * @var BlogReviewRepository
     */
    private $postReviewRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * BlogReviewController constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $this->postReviewRepository = $this->objectManager->getRepository(BlogReview::class);
        $this->postRepository = $this->objectManager->getRepository(BlogPost::class);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->request->get('page');
        $postId = $request->request->get('object_id');

        if ($page >= 0 && $postId) {
            $reviews = $this->postReviewRepository->pagination($postId, $page);
            $postReviews = (new ReviewsNormalizer())->normalize($reviews);

            return new JsonResponse($postReviews);
        }

        throw new BadRequestHttpException('parameters not founded');
    }

    /**
     * Create Blog review
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

        /** @var BlogPost $post */
        if (!$post = $this->postRepository->find($id)) {
            return new JsonResponse('Post not found', 404);
        }

        $rating = $request->request->get('rating');
        $title = $request->request->get('title');
        $comment = $request->request->get('comment');

        if (!$rating || !$title || !$comment) {
            return new JsonResponse('Bad parameters rating or title or comment ', 404);
        }


        $roles = [UserMarketing::MARKETING_ROLE, AdminUser::DEFAULT_ADMIN_ROLE];
        $data = [
            'blog' => $post,
            'user' => $user,
            'comment' => $comment
        ];
        $this->get('app.service.admin_user')->sendEmail($roles, 'app_blog_review_created', $data);

        $postReview = new BlogReview();
        $postReview->setAuthor($user->getCustomer());
        $postReview->setRating($rating);
        $postReview->setTitle($title);
        $postReview->setComment($comment);
        $postReview->setReviewSubject($post);
        $postReview->setStatus(BlogReview::STATUS_NEW);
        $postReview->setCreatedAt(new \DateTime('now'));

        $this->objectManager->persist($postReview);
        $this->objectManager->flush();

        return new JsonResponse('Review added', 201);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function starRating($id)
    {
        $rating = $this->postReviewRepository->starRatingByBlog($id);

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
