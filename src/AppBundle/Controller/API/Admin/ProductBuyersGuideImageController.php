<?php
/**
 * Created by PhpStorm.
 * User: m153
 * Date: 3/4/19
 * Time: 1:29 PM
 */

namespace AppBundle\Controller\API\Admin;


use AppBundle\Entity\BuyersGuideImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductBuyersGuideImageController extends Controller
{
    public function removeImage($id = 0)
    {
        $repository = $this->get('app.repository.buyers_guide_image');
        /** @var BuyersGuideImage $image */
        if ($image = $repository->find($id)) {
            $repository->remove($image);
        }
        return new JsonResponse($id);
    }
}