<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxon;
use AppBundle\Entity\TaxonProductRelated;
use AppBundle\Form\Type\TaxonProductRelatedType;
use AppBundle\Form\Type\TaxonProductSortType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxonProductRelatedController extends ResourceController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param EngineInterface $templatingEngine
     */
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    public function indexAction(Request $request): Response
    {
        $em = $this->getDoctrine();
        $taxonProductRepository = $em->getRepository(TaxonProductRelated::class);
        $taxonRepository = $this->getDoctrine()->getRepository(Taxon::class);

        $form = $this->createForm(TaxonProductSortType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $data = $form->getData();
            $category = $data['category'];
            $taxon = isset($category) ? $taxonRepository->findOneBy(['code' => $category->getCode()]) : null;
            if ($taxon) {
                $taxonProduct = $taxonProductRepository->findBy(['category' => $taxon->getId()]);
            } else {
                $taxonProduct = $taxonProductRepository->findAll();
            }

            return $this->templatingEngine->renderResponse(":TaxonProductRecomended:index.html.twig", [
                'taxon_products' => $taxonProduct ?? null,
                'form' => $form->createView()
            ]);
        }

        $taxonProduct = $taxonProductRepository->findAll();

        return $this->templatingEngine->renderResponse(":TaxonProductRecomended:index.html.twig", [
            'taxon_products' => $taxonProduct ?? null,
            'form' => $form->createView()
        ]);
    }

    public function createAction(Request $request): Response
    {
        $taxox_product = new TaxonProductRelated;

        $form = $this->createForm(TaxonProductRelatedType::class, $taxox_product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taxox_product);
            $em->flush();

            return $this->redirectToRoute('app_taxon_product_index_controller_admin');
        }

        return $this->templatingEngine->renderResponse(":TaxonProductRecomended:create.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, $id): Response
    {
        $taxox_product = $this->getDoctrine()->getRepository(TaxonProductRelated::class)->find($id);

        $form = $this->createForm(TaxonProductRelatedType::class, $taxox_product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($taxox_product);
            $em->flush();

            return $this->redirectToRoute('app_taxon_product_index_controller_admin');
        }

        return $this->templatingEngine->renderResponse(":TaxonProductRecomended:edit.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    public function showProductDependOnTaxon(Request $request)
    {
        $data = $request->attributes->all();
        $em = $this->getDoctrine();
        $products = [];

        if (isset($data['taxon'])) {
            if ($taxon = $em->getRepository(Taxon::class)->findOneBy(['code' => $data['taxon']])) {
                /* @var $taxonProducts TaxonProductRelated */
                $taxonProducts = $em->getRepository(TaxonProductRelated::class)->
                findOneBy(['category' => $taxon->getId()]);
                if ($taxonProducts) {
                    $products = $taxonProducts->getProducts()->toArray();
                }
            }
        }

        return $this->templatingEngine->renderResponse(":TaxonProductRecomended/Shop:index.html.twig", [
            'products' => $this->get('app.service.interesting_in_compatible')->getCompatible($products, $request->getSession()),
        ]);
    }

    public function delete($id)
    {
        $em = $this->getDoctrine();
        $taxox_product = $em->getRepository(TaxonProductRelated::class)->find($id);

        $em->getManager()->remove($taxox_product);
        $em->getManager()->flush();

        return $this->redirectToRoute('app_taxon_product_index_controller_admin');
    }
}
