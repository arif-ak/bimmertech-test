<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use AppBundle\Entity\BlogProducts;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogController
 * @package AppBundle\Controller
 */
class BlogController extends Controller
{
    /**
     * Show post by slug
     *
     * @param Request $request
     * @param $slug
     * @return Response
     */
    public function getBySlug(Request $request, $slug)
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);

        $post = $repository->findOneBy(['slug' => $slug]);
        /** @var BlogPost $post */
        if (!$post) {
            return $this->render('@SyliusShop/error404.html.twig', [], (new Response('page not found', Response::HTTP_NOT_FOUND)));
        }

        $tags = $post->getMetaTags();
        $explodeTags = array_map('trim', explode(",", $tags));
        $relatedPost = [];
        if (count($explodeTags) > 0 && !$explodeTags[0] == "") {
            $relatedPost = $repository->findRelatedPosts($explodeTags, $post->getId());
        }

        $post->setCounter($post->getCounter() + 1);
        $em->getManager()->flush();

        $interestedIn = $post->getProductRelated() ? $post->getProductRelated()->getProducts()->toArray() : [];

        return $this->render('Pages/Blog/Show.htm.twig', [
            'post' => $post,
            'relatedPost' => $relatedPost,
            'interestedIn' => $this->get('app.service.interesting_in_compatible')->getCompatible($interestedIn, $request->getSession())
        ]);
    }

    /**
     * @return Response
     */
    public function latest()
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);

        $latest = $repository->getLatestMainPage();
        return $this->render('Pages/Blog/Home/index.html.twig', [
            'post' => $latest
        ]);
    }

    /**
     *  Show all posts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogProducts::class);
        $interestedIn = $repository->findAll();


        $products = (new ArrayCollection($interestedIn))->map(function ($blogProduct) {
            return $blogProduct->getProduct();
        })->toArray();

        return $this->render('Pages/Blog/index.html.twig', [
            'interestedIn' => $this->get('app.service.interesting_in_compatible')->getCompatible($products, $request->getSession()),
            'latest' => $this->getLastPost(),
        ]);
    }

    /**
     * @return array
     */
    private function getLastPost()
    {
        $array = [];
        $i = 1;
        $em = $this->getDoctrine();
        $repository = $em->getRepository(BlogPost::class);
        $lastPost = $repository->getLatestMainPage(12);

        /** @var BlogPost $item */
        foreach ($lastPost as $item) {
            $ratio = 1;
            $image = $item->getBlogPostImage()->filter(function ($postImage) {
                /** @var BlogPostImage $postImage */
                if ($postImage->getAspectRatio() === '1:1') {
                    return $postImage;
                }
            })->first();
            if ($i == 1 || $i == 6) {
                $ratio = 2;
                $image = $item->getBlogPostImage()->filter(function ($postImage) {
                    /** @var BlogPostImage $postImage */
                    if ($postImage->getAspectRatio() === '2:1') {
                        return $postImage;
                    }
                })->first();
            }
            $array[] = [
                'post' => $item,
                'ratio' => $ratio,
                'image' => $image
            ];
            $i++;
            if ($i == 7) {
                $i = 1;
            }
        }
        return $array;
    }
}
