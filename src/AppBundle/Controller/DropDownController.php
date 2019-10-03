<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ProductController
 *
 * @package AppBundle\Controller
 */
class DropDownController extends ResourceController
{
    /**
     * @param Request $request
     * @return Response
     * @throws HttpException
     */
    public function updateAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $resource = $form->getData();

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

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource);
        if ($initializeEvent->hasResponse()) {
            return $initializeEvent->getResponse();
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'));

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws HttpException
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

        $delete = $this->deleteResource($resource);

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
     * @param DropDown $dropDown
     * @return mixed|void
     */
    public function deleteResource(DropDown $dropDown)
    {

        $options = $dropDown->getDropDownOptions()->toArray();

        $options = array_map(function ($option) {
            /** @var DropDownOption $option */
            return $option->getId();
        }, $options);

        $dropDownOptions = $this->container->get('app.repository.order_item_drop_down_option')
            ->createQueryBuilder('o')
            ->andWhere('o.dropDownOption IN (:ids)')
            ->setParameter('ids', $options)
            ->getQuery()
            ->getResult();


        if ($dropDownOptions) {
            return $dropDownOptions;
        }
        return;
    }
}
