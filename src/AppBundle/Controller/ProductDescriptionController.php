<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Entity\ProductDescription;
use AppBundle\Form\Type\ProductDescriptionType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductDescriptionController extends Controller
{
    public function index(Request $request, $productId)
    {
        $descriptions = $this->get('app.repository.product_description')->getByProductId($productId);

        $forms = [];

        foreach ($descriptions as $description) {
            $forms[] = $this->createForm(ProductDescriptionType::class, $description)->createView();
        }

        return $this->render(':Admin/ProductDescription:index.html.twig', [
            'descriptionForms' => $forms
        ]);

    }

    public function add(Request $request,  $product)
    {
        $productDescription = new ProductDescription();
        $form = $this->createForm(ProductDescriptionType::class, $productDescription);

        return $this->render(':Admin/ProductDescription:_add_form.html.twig', [
            'addForm' => $form,
            'product'=>$product
        ]);
    }


    /**
     * Delete product description
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        if ($description = $this->get('app.repository.product_description')->find($id)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($description);
            $em->flush();
        }

        return new JsonResponse('Deleted', 204);
    }
}