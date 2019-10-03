<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Taxon;
use ArrayIterator;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ProductController
 *
 * @package AppBundle\Controller
 */
class ProductController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $data = [];
        $channelRepository = $this->get('sylius.repository.channel');

        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByHostname($request->getHost());
        if (!$channel) {
            $channelCode = $request->cookies->get('_channel_code');
            $channelCode = ($channelCode) ? $channelCode : 'bimmer_tech';
            /** @var ChannelInterface $channel */
            $channel = $channelRepository->findOneByCode($channelCode);
        }

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        /** @var ResourceGridView $resources */
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);
        $view = View::create($resources);

        $session = new Session();
        $sessionProducts = $session->get('vincheck');




        if ($taxonCode = $request->attributes->get('slug')) {
            /** @var Taxon $taxon */
            $taxon = $this->get('sylius.repository.taxon')->findOneBySlug($taxonCode, 'en_US');


            if ($taxon) {
                if ($taxon->getLevel() == Taxon::TAXON_CONTAINER) {
                    $products = $taxon->getProducts();
                    if ($sessionProducts !== null) {
                        /** @var Product $product */
                        foreach ($products as $product) {
                            $this->get('app.vin_check.compatibility_products')
                                ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
                            if ($product->getCompatibility() == ProductInterface::COMPATIBILITY_YES) {
                                return $this->redirectToRoute('sylius_shop_product_show', ['slug' => $product->getSlug()]);
                            }
                        }
                    }

                    if ($sessionProducts !== null) {
                        $compatibility = $sessionProducts['compatibility'] == Product::COMPATIBILITY_NOT_SURE ?
                            Product::COMPATIBILITY_NOT_SURE : Product::COMPATIBILITY_NO;
                    }

                    $popupCollection = $taxon->getPopupOption();

                    $carModels = $this->get('app.service.car_model')->carModelProductContainer($products);

                    return $this->render('Product/taxon_container.html.twig', [
                        'taxon' => $taxon,
                        'parentTaxon' => $taxon->getParent(),
                        'product' => count($taxon->getProducts()) ? $taxon->getProducts()->first() : null,
                        'compatibility' => isset($compatibility) ? $compatibility : null,
                        'popup_options' => $popupCollection,
                        'carModels' => $carModels
                    ]);
                } else {

                    $data = [
                        'taxon' => $taxon,
                        'containers' => $taxon->getChildren(),
                        'code' => $taxon->getCode(),
                        'taxonName' => $taxon->getName(),
                    ];
                }
            }
        }

        if ($sessionProducts !== null) {
            $compatibilityService = $this->get('app.vin_check.compatibility_products');
            $compatibilityService->setAllCompatibility($resources, $sessionProducts['products'],
                $sessionProducts['compatibility']);
            if (isset($taxon)) {
                foreach ($taxon->getChildren() as $item) {
                    if ($item->isContainer()) {
                        $containerProducts = $this->get('sylius.repository.product')
                            ->createShopListQueryBuilder($channel, $item, 'en_US', ['name' => 'ASC'])
                            ->getQuery()
                            ->getResult();
                        $containerProd = $compatibilityService
                            ->compatibilityContainer($containerProducts, $sessionProducts['products'],
                                $sessionProducts['compatibility']);
                        if ($containerProd) {
                            /** @var ArrayIterator $iterator */
                            $iterator = $resources->getData()->getCurrentPageResults();
                            $iterator->append($containerProd);
                        }
                    }
                }
            }

            $compatibilityService->compatibilityContainer($resources, $sessionProducts['products'],
                $sessionProducts['compatibility']);
        }

        if ($configuration->isHtmlRequest()) {
            $data += [
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resources' => $resources,
                $this->metadata->getPluralName() => $resources,
            ];

            $view->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData($data);
        }


        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        /** @var Product $resource */
        $resource = $this->findOr404($configuration);

        $session = new Session;
        $sessionProducts = $session->get('vincheck');

        if ($sessionProducts !== null) {
            $this->get('app.vin_check.compatibility_products')
                ->setOneCompatibility($resource, $sessionProducts['products'], $sessionProducts['compatibility']);
        }

        if ($resource->getBuyersRelated()->count() == 1 || $resource->getBuyersRelated()->count() == 2) {
            $buyersGuide = $this->get('app.service.buyers_guide')->buyersGuideHelper($resource);
        }

        if ($resource->getMainTaxon()) {
            if ($resource->getMainTaxon()->getLevel() == Taxon::TAXON_CONTAINER) {
                $taxon = $resource->getMainTaxon();
                $parentTaxon = $taxon->getParent();
            }
        }

        $popupCollection = $resource->getProductPopupOption();

        $carModels = $this->get('app.service.car_model')->carModelProduct($resource);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);
        $view = View::create($resource);


        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                ->setTemplateVar($this->metadata->getName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $resource,
                    'buyers_guide' => isset($buyersGuide) ? $buyersGuide : null,
                    'popup_options' => $popupCollection,
                    'parentTaxon' => isset($parentTaxon) ? $parentTaxon : null,
                    'carModels' => $carModels,
                    'interestingProducts' => $this->getInterestingProducts($resource, $request),
                    $this->metadata->getName() => $resource,
                ]);
        }

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::DELETE);
        $resource = $this->findOr404($configuration);

        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid((string)$resource->getId(), $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource);

        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectHandler->redirectToIndex($configuration, $resource);
        }

        $delete = $this->get('app.service.delete_product')->deleteProduct($resource);

        if ($delete) {
            $this->flashHelper->addErrorFlash($configuration, 'delete_error');

            return $this->redirectHandler->redirectToReferer($configuration);
        }


        try {
            $this->resourceDeleteHandler->handle($resource, $this->repository);
        } catch (DeleteHandlingException $exception) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle(
                    $configuration,
                    View::create(null, $exception->getApiResponseCode())
                );
            }

            $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource);

        if ($postEvent->hasResponse()) {
            return $postEvent->getResponse();
        }

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * Generane new entity
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);

        /** @var Product $newResource */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);
        $products = $this->repository->findAll();

        $products ? $last = end($products)->getId() + 2 : $last = 1;


        /** Generate product */
        $newResource->setCode('new_product_' . $last);
        $newResource->setName('new_product_' . $last);
        $newResource->getVariants()->first()->setCode($newResource->getCode());
        $newResource->setSlug('new-product-' . $last);

        $this->repository->add($newResource);
        /** And generate product */

        $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($newResource, Response::HTTP_CREATED));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);

        if ($postEvent->hasResponse()) {
            return $postEvent->getResponse();
        }


        return $this->redirectHandler->redirectToResource($configuration, $newResource);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return array
     */
    private function getInterestingProducts($product, Request $request)
    {
        if ($products = $product->getInterestingProducts()->toArray()) {
            return $this->get('app.service.interesting_in_compatible')->getCompatible($products, $request->getSession());
        }
        return [];
    }
}
