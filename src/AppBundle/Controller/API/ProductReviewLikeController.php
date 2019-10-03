<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\ProductReviewLike;
use AppBundle\Serializer\Normalizer\ReviewsNormalizer;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ProductReviewController
 * @package AppBundle\Controller\API
 */
class ProductReviewLikeController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->request->get('page');
        $productId = $request->request->get('product_id');

        if ($page >= 0 && $productId) {
            $productReviewRepository = $this->container->get('sylius.repository.product_review');
            $reviews = $productReviewRepository->pagination($productId, $page);

            $productReviews = (new ReviewsNormalizer())->normalize($reviews);

            return new JsonResponse($productReviews);
        }

        throw new BadRequestHttpException('parameters not founded');
    }

    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productReviewRepository = $this->container->get('sylius.repository.product_review');
        $productReviewLikeRepository = $em->getRepository(ProductReviewLike::class);

        $data = $request->request->all();

        $reviewId = isset($data['review_id']) ? $data['review_id'] : null;
        $clientToken = isset($data['client_token']) ? $data['client_token'] : null;
        $isLiked = isset($data['is_liked']) ? $data['is_liked'] : null;

        $securityContext = $this->container->get('security.authorization_checker');
        $review = $productReviewRepository->find($reviewId);

        if ($anonymousUser = $securityContext->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $productReviewLike = $productReviewLikeRepository
                ->findByCustomerAndReview($review->getId(), $user->getCustomer()->getId(), $clientToken);
        } else {
            $productReviewLike =
                $productReviewLikeRepository->findByClientTokenAndReview($review->getId(), $clientToken);
        }

        if ($review && $isLiked == true) {
            $productLike = new ProductReviewLike();

            if (count($productReviewLike) >= 1 && $isLiked == true) {
                return new JsonResponse(['error' => 'Like for this review and user already exist'], 400);
            }

            if (empty($clientToken)) {
                $clientToken = $this->generateRandomString(32);
            }

            if ($anonymousUser = $securityContext->isGranted('ROLE_USER')) {
                $user = $this->getUser();
                $productLike->setCustomer($user->getCustomer());
            } else {
                $productLike->setClientToken($clientToken);
            }

            $productLike->setReview($review);

            $em->persist($productLike);
            $em->flush();

            return new JsonResponse(
                ['data' =>
                    [
                        "message" => 'successful created',
                        "token" => $clientToken
                    ]
                ]
            );
        } elseif ($review && $isLiked == false && $clientToken) {
            if (count($productReviewLike) >= 1) {
                foreach ($productReviewLike as $item) {
                    $em->remove($item);
                }
                $em->flush();

                return new JsonResponse(['data' => 'successful removed']);
            } else {
                return new JsonResponse(['error' => "like did not find"], 404);
            }
        }

        throw new BadRequestHttpException('required parameters did not find');
    }

    public function clientTokenFilter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productReviewLikeRepository = $em->getRepository(ProductReviewLike::class);

        $user = $this->getUser();
        $clientToken = $request->request->get('client_token');
        $productId = $request->request->get('object_id');

        if ((!empty($clientToken) || $user) && !empty($productId)) {
            if ($user instanceof ShopUser) {
                $clientLikes = $productReviewLikeRepository->
                findByCustomer($user->getCustomer()->getId(), $clientToken, $productId);
            } else {
                $clientLikes = $productReviewLikeRepository->findByClientToken($clientToken, $productId);
            }

            $reviewsId = array_map(function ($clientLike) {
                /** @var ProductReviewLike $clientLike */
                $review = $clientLike->getReview();

                if ($review->getStatus() === 'accepted') {
                    return $review->getId();
                }
            }, $clientLikes);

            // get unique result
            $results = array_unique($reviewsId, SORT_NUMERIC);

            // get values form array
            $withoutKeys = [];
            foreach ($results as $key => $value) {
                $withoutKeys[] = $value;
            }

            return new JsonResponse(['data' => $withoutKeys]);
        }

        return new JsonResponse(['error' => "required parameter 'client_token' or 'object_id' did not find"], 400);
    }

    public function generateRandomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
