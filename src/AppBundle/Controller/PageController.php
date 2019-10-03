<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{

    /**
     * @var EntityRepository
     */
    private $pageRepository;

    /**
     * PageController constructor.
     *
     * @param EntityRepository $pageRepository
     */
    public function __construct(EntityRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param $slug
     * @return Response
     */
    public function getBySlug($slug)
    {
        if (!$page = $this->pageRepository->findOneBySlug($slug)) {
            return $this->render('@SyliusShop/error404.html.twig', [],(new Response('page not found', Response::HTTP_NOT_FOUND)));
        }

        return $this->render('Pages/Page/Show.htm.twig', [
            'page' => $page
        ]);
    }
}