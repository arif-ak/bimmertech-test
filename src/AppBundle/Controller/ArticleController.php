<?php

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Webburza\Sylius\ArticleBundle\Controller\ArticleController as BaseArticleController;

/**
 * Class ArticleController
 * @package AppBundle\Controller
 */
class ArticleController extends BaseArticleController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexHomeAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        /** @var ResourceGridView $resources */
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);
        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'articles' => $resources,
                    $this->metadata->getPluralName() => $resources,
                ]);
        }
        return $this->viewHandler->handle($configuration, $view);
    }
}