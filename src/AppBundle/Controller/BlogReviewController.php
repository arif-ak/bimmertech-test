<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlogReviewController extends Controller
{
    /**
     * @param $slug
     * @return Response
     */
    public function getBySlug($slug)
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);

        /** @var BlogPost $post */
        $post = $repository->findOneBySlug($slug);


        /** @var BlogPost $post */
        if (!$post) {
            return new Response('Post not found', 404);
        }

        $post->setCounter($post->getCounter() + 1);
        $em->getManager()->flush();

        $tags = $post->getMetaTags();
        $explodeTags = array_map('trim', explode(",", $tags));
        $relatedPost = $repository->findRelatedPosts($explodeTags, $post->getId());

        return $this->render('Blog/Show.htm.twig', [
                'post' => $post,
                'relatedPost' => $relatedPost
            ]
        );
    }

    public function apply($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(BlogReview::class);

        $review = $repository->find($id);
        $review->setStatus(BlogReview::STATUS_ACCEPTED);

        $em->flush();

        return $this->redirectToRoute('app_admin_blog_post_review_index');
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reject($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(BlogReview::class);

        $review = $repository->find($id);
        $review->setStatus(BlogReview::STATUS_REJECTED);

        $em->flush();

        return $this->redirectToRoute('app_admin_blog_post_review_index');
    }

    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('Blog/index.html.twig');
    }
}
