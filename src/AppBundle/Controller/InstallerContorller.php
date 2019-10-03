<?php
/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 09.10.2018
 * Time: 12:50
 */

namespace AppBundle\Controller;

use Symfony\Component\Templating\EngineInterface;

class InstallerContorller
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

        if (isset($data['taxon'])) {
            $taxon = $em->getRepository(Taxon::class)->findOneBy(['code' => $data['taxon']]);
            if ($taxon) {
                /* @var $taxonProduct TaxonProductRelated */
                $taxonProducts = $em->getRepository(TaxonProductRelated::class)->
                findBy(['category' => $taxon->getId()]);

                if ($taxonProducts) {
                    $arrayTaxonProducts = [];
                    foreach ($taxonProducts as $taxonProduct) {
                        $arrayTaxonProducts[] = $taxonProduct->getProduct();
                    }

                    return $this->templatingEngine->renderResponse(":TaxonProductRecomended/Shop:index.html.twig", [
                        'products' => $arrayTaxonProducts,
                    ]);
                } else {
                    $products = $em->getRepository(Product::class)->findBy(['recomended' => 1]);

                    return $this->templatingEngine->renderResponse(":TaxonProductRecomended/Shop:index.html.twig", [
                        'products' => $products,
                    ]);
                }
            } else {
                $products = $em->getRepository(Product::class)->findBy(['recomended' => 1]);

                return $this->templatingEngine->renderResponse(":TaxonProductRecomended/Shop:index.html.twig", [
                    'products' => $products,
                ]);
            }
        } else {
            $products = $em->getRepository(Product::class)->findBy(['recomended' => 1]);

            return $this->templatingEngine->renderResponse(":TaxonProductRecomended/Shop:index.html.twig", [
                'products' => $products,
            ]);
        }
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
