<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxon;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxonomyController extends ResourceController
{
    const CONTAINER = 'container';
    const CATEGORY = 'category';

    public function preCreateAction(Request $request, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Taxon::class);
        $latestCategory = $repository->findBy([], ['id' => 'DESC'], 1);

        $taxon = new Taxon();
        $id = 1;
        if (count($latestCategory) > 0) {
            $id = $latestCategory[0]->getId();
        }


        if ($type == self::CONTAINER) {
            $taxon->setIsContainer(true);
            $taxon->setCurrentLocale('en_US');
            $taxon->setName('ContainerName' . $id);
            $taxon->setSlug('container_name' . $id);
            $taxon->setCode('container_name' . $id);
        } elseif ($type == self::CATEGORY) {
            $taxon->setCurrentLocale('en_US');
            $taxon->setName('CategoryName' . $id);
            $taxon->setSlug('category_name' . $id);
            $taxon->setCode('category_name' . $id);
        }

        $em->persist($taxon);
        $em->flush();

        $id = $taxon->getId();

        return $this->redirectToRoute('sylius_admin_taxon_update', ['id' => $id, 'type' => $type]);
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
        $resource = $this->findOr404($configuration);

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
    public function updateAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);
        $resource->type = $request->query->get('type');

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $resource = $form->getData();
            $name = $resource->getName();
            $resource->setProductName($name);

            /** @var ResourceControllerEvent $event */
            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectHandler->redirectToResource($configuration, $resource);
            }

            try {
                $this->resourceUpdateHandler->handle($resource, $configuration, $this->manager);
            } catch (UpdateHandlingException $exception) {
                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle(
                        $configuration,
                        View::create($form, $exception->getApiResponseCode())
                    );
                }

                $this->flashHelper->addErrorFlash($configuration, $exception->getFlash());

                return $this->redirectHandler->redirectToReferer($configuration);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource);

            if (!$configuration->isHtmlRequest()) {
                $view = $configuration->getParameters()->get('return_content', false) ? View::create($resource, Response::HTTP_OK) : View::create(null, Response::HTTP_NO_CONTENT);

                return $this->viewHandler->handle($configuration, $view);
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);

            if ($postEvent->hasResponse()) {
                return $postEvent->getResponse();
            }

            if ($request->query->get('type') == self::CONTAINER) {
//               $resource->
            }

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $showContainerBlock = false;
        if ($resource->getLevel() == Taxon::TAXON_CONTAINER || $resource->type == self::CONTAINER || $resource->isContainer()) {
            $showContainerBlock = true;
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource);
        if ($initializeEvent->hasResponse()) {
            return $initializeEvent->getResponse();
        }

        $view = View::create()
            ->setData([
                'showContainerBlock' => $showContainerBlock,
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'));

        return $this->viewHandler->handle($configuration, $view);
    }
    
}
