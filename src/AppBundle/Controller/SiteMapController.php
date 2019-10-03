<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\Taxon;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteMapController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('SiteMap.html.twig', [
            'pages' => $this->getPages(),
            'blogCategories' => $this->getPosts(),
            'shop' => $this->getProducts(),
        ]);
    }

    /**
     * @return array
     */
    private function getProducts()
    {
        $result = [];
        $taxons = $this->get('sylius.repository.taxon')->findBy(['enabled' => true, 'level' => 1], ['position' => 'ASC']);

        /** @var Taxon $taxon */
        foreach ($taxons as $taxon) {

            $products = $this->get('sylius.repository.product')->findBy([
                'enabled' => true,
                'mainTaxon' => $taxon
            ]);

            $result[] = [
                'name' => strtoupper($taxon->getName()),
                'slug' => $taxon->getSlug(),
                'products' => $products,
                'redirect' => $this->replaceUrl($taxon),
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getPosts()
    {
        $result = [];

        /** @var BlogCategory $category */
        $categories = $this->get('app.repository.blog_category')->findByEnabled(true);
        foreach ($categories as $category) {
            $result[strtoupper($category->getName())] =
                $this->get('app.repository.blog_post')->findBy([
                    'enabled' => true,
                    'blogCategory' => $category
                ]);
        }
        $result['No CATEGORY'] = $this->get('app.repository.blog_post')->findBy([
            'enabled' => true,
            'blogCategory' => null
        ]);

        return $result;
    }

    /**
     * @return array
     */
    private function getPages()
    {
        $pages = [
            'our_company' => [],
            'information' => [],
        ];
        $pageRepository = $this->get('app.repository.custom_page');
        $pagesCollection = new ArrayCollection($this->get('app.repository.custom_page')->findAll());

        if ($about_us = $pageRepository->findOneBySlug('about-us')) {
            $pages['our_company'][] = $about_us;
            $pagesCollection->removeElement($about_us);
        }
        $pages['information'] = $pagesCollection->toArray();

        return $pages;
    }

    /**
     * @param Taxon $taxon
     * @return string
     */
    private function replaceUrl(Taxon $taxon)
    {
        switch ($taxon->getCode()) {
            case 'audio':
                return 'https://www.bimmer-tech.net/speakers-and-audio-amplifier-for-bmw';
            case 'idrive':
                return 'https://www.bimmer-tech.net/bmw-idrive-coding';
            default:
                return '/category-' . $taxon->getSlug();
        }
    }
}