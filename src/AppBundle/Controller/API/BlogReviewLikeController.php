<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\BlogReview;
use AppBundle\Entity\BlogReviewLike;
use AppBundle\Serializer\Normalizer\ReviewsNormalizer;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BlogReviewLikeController
 * @package AppBundle\Controller\API
 */
class BlogReviewLikeController extends Controller
{
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $blogReviewRepository = $em->getRepository(BlogReview::class);
        $blogReviewLikeRepository = $em->getRepository(BlogReviewLike::class);

        $data = $request->request->all();

        $reviewId = isset($data['review_id']) ? $data['review_id'] : null;
        $clientToken = isset($data['client_token']) ? $data['client_token'] : null;
        $isLiked = isset($data['is_liked']) ? $data['is_liked'] : null;

        $securityContext = $this->container->get('security.authorization_checker');
        $review = $blogReviewRepository->find($reviewId);

        if ($anonymousUser = $securityContext->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $blogReviewLike = $blogReviewLikeRepository
                ->findByCustomerAndReview($review->getId(), $user->getCustomer()->getId(), $clientToken);
        } else {
            $blogReviewLike =
                $blogReviewLikeRepository->findByClientTokenAndReview($review->getId(), $clientToken);
        }

        if ($review && $isLiked == true) {
            $productLike = new BlogReviewLike();

            if (count($blogReviewLike) >= 1 && $isLiked == true) {
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
            if (count($blogReviewLike) >= 1) {
                foreach ($blogReviewLike as $item) {
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
        $blogReviewLikeRepository = $em->getRepository(BlogReviewLike::class);

        $user = $this->getUser();
        $clientToken = $request->request->get('client_token');
        $blogId = $request->request->get('object_id');

        if ((!empty($clientToken) || $user) && !empty($blogId)) {
            if ($user instanceof ShopUser) {
                $clientLikes = $blogReviewLikeRepository->
                findByCustomer($user->getCustomer()->getId(), $clientToken, $blogId);
            } else {
                $clientLikes = $blogReviewLikeRepository->findByClientToken($clientToken, $blogId);
            }

            $reviewsId = array_map(function ($clientLike) {
                /** @var BlogReviewLike $clientLike */
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
