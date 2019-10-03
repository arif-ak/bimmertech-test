<?php

namespace AppBundle;

use AppBundle\Entity\BlogPost;
use Doctrine\Common\EventSubscriber;
use FOS\ElasticaBundle\Doctrine\Listener;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use FOS\ElasticaBundle\Provider\IndexableInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PostIndexListener extends Listener implements EventSubscriber
{
    protected $propertyAccessor;

    private $indexable;
    private $config;

    public function __construct(
        ObjectPersisterInterface $postPersister,
        IndexableInterface $indexable,
        array $config
    ) {
        $this->objectPersister = $postPersister;
        $this->indexable = $indexable;
        $this->config = $config;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getSubscribedEvents()
    {
        return ['postPersist', 'postUpdate', 'preRemove', 'preFlush', 'postFlush'];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof BlogPost) {
            if ($this->objectPersister->handlesObject($entity)) {
                if ($this->isObjectIndexable($entity)) {
                    $this->scheduledForInsertion[] = $entity;
                }
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof BlogPost) {
            if ($this->objectPersister->handlesObject($entity)) {
                if ($this->isObjectIndexable($entity)) {
                    $this->scheduledForUpdate[] = $entity;
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof BlogPost) {
            if ($this->objectPersister->handlesObject($entity)) {
                if ($identifierValue = $this->propertyAccessor->getValue($entity, $this->config['identifier'])) {
                    $this->scheduledForDeletion[] = $identifierValue;
                }
            }
        }
    }

    private function isObjectIndexable($object)
    {
        return $this->indexable->isObjectIndexable(
            $this->config['index'],
            $this->config['type'],
            $object
        );
    }
}