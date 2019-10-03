<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Taxon;
use AppBundle\Form\Type\VinCheckProductType;
use AppBundle\Repository\TaxonRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VinCheckController
 * @package AppBundle\Controller
 */
class VinCheckController extends ResourceController
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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearVin(Request $request)
    {
        $vin = $request->getSession();
        $vin->remove('vincheck');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @param Request $request
     * @param string $vin
     * @param int $productId
     *
     * @return Response
     */
    public function checkProductAction(Request $request)
    {
        $form = $this->createForm(VinCheckProductType::class);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return new JsonResponse(['error' => $form->getErrors()], Response::HTTP_BAD_REQUEST);
        } else {
            $vin = $form['vin']->getData();
            $productId = $form['productId']->getData();
            $response = $this->get('app.vin_check.vin_check')->checkByProduct($vin, $productId);

            if ($response) {
                return $this->templatingEngine->renderResponse(':Blocks:checkIconTrue.html.twig');
            } else {
                return $this->templatingEngine->renderResponse(':Blocks:checkIconFalse.html.twig');
            }

//            return new JsonResponse(['data' => $response], Response::HTTP_OK);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveProductAction(Request $request)
    {
        $vin = $request->get('vin');
        $taxonSlug = null !== $request->get('parameters') ? $request->get('parameters') : null;

        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        try {
            $session = $this->get('app.vin_check.vin_check_session')->saveToSessionByVin($vin, $channel);
            $this->addFlash('success', 'We have checking all possibility products!');
        } catch (Exception $exception) {
            $this->addFlash('error', 'Something went wrong');
        }

        if ($taxonSlug) {
            $sessionProducts = $session->get('vincheck');
            /** @var TaxonRepository $taxonRepository */
            $taxonRepository = $this->get('sylius.repository.taxon');
            /** @var Taxon $taxon */
            $taxon = $taxonRepository->findContainerBySlug($taxonSlug);
            if ($taxon) {
                $isCompatibility = false;
                $products = $taxon->getProducts();
                /** @var Product $product */
                foreach ($products as $product) {
                    $this->get('app.vin_check.compatibility_products')
                        ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
                    if ($product->getCompatibility() == ProductInterface::COMPATIBILITY_YES) {
                        return $this->redirectToRoute('sylius_shop_product_show', ['slug' => $product->getSlug()]);
                    }
                }
            }
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveProductManualAction(Request $request)
    {
        $model = $request->get('model');
        $year = $request->get('year');

        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        try {
            $this->get('app.vin_check.vin_check_session')->saveToSessionByManual($model, $year, $channel);
            $this->addFlash('success', 'We have checking all possibility products!');
        } catch (Exception $exception) {
            $this->addFlash('error', 'Something went wrong');
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
