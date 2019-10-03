<?php

namespace AppBundle\Repository;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class WarehouseRepository
 * @package AppBundle\Repository
 */
class MediaImageUsedRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getUsed(): array
    {
        $array = [];
        $result = $this->createQueryBuilder('u')
            ->select('u.name')
            ->addGroupBy('u.name')
            ->getQuery()
            ->getResult();

        if ($result) {
            foreach ($result as $item) {
                $array[] = $item['name'];
            }
        }
        return $array;
    }
}
