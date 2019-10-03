<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.02.2019
 * Time: 12:37
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Entity\ProductDescription;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JoinProductController extends Controller
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * JoinProductController constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return Response
     */
    public function rewriteAction(){
       $products = $this->objectManager->getRepository(Product::class)->findAll();
        foreach ($products as $product){
            /** @var Product $product */
            $productDescriptions = $product->getProductDescriptions();
            $productInstallers = $product->getProductInstallers();
            $this->concatenateDescription($product,$productDescriptions);
            $this->concatenateInstaller($product,$productInstallers);
       }

        $this->objectManager->flush();

        return new Response('success');
    }

    /**
     * @param Product $product
     * @param $productTexts
     */
    public function concatenateDescription(Product $product, $productTexts){
        $concatenate = '';
        foreach ($productTexts as $productText ){
            /** @var ProductDescription $productText */
            $header = $productText->getHeader();
            $subHeader = $productText->getSubHeader();
            $description = $productText->getDescription();

            $concatenate .= $concatenate.' '.$header .' ' . $subHeader .' '. $description;

            $product->setDescription($concatenate);

            $this->objectManager->persist($product);
        }

    }

    /**
     * @param Product $product
     * @param $productTexts
     */
    public function concatenateInstaller(Product $product, $productTexts){
        $concatenate = '';
        foreach ($productTexts as $productText ){
            /** @var ProductDescription $productText */
            $header = $productText->getHeader();
            $subHeader = $productText->getSubHeader();
            $description = $productText->getDescription();

            $concatenate .= $concatenate.' '.$header .' ' . $subHeader .' '. $description;

            $product->setInstaller($concatenate);

            $this->objectManager->persist($product);
        }

    }
}