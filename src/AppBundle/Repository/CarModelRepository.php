<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Class SliderRepository
 * @package AppBundle\Repository
 */
class CarModelRepository extends EntityRepository implements RepositoryInterface
{


    /**
     * @param array $criteria
     * @param array $sorting
     *
     * @return iterable
     */
    public function createPaginator(array $criteria = [], array $sorting = []): iterable
    {

    }

    /**
     * @param ResourceInterface $resource
     */
    public function add(ResourceInterface $resource): void
    {

    }

    /**
     * @param ResourceInterface $resource
     */
    public function remove(ResourceInterface $resource): void
    {

    }
}
