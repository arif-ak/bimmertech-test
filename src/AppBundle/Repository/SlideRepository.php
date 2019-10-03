<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SlideRepository
 * @package AppBundle\Repository
 */
class SlideRepository extends EntityRepository
{
    /**
     * Find Slide by slider code
     *
     * @param $sliderCode
     * @return mixed
     */
    public function getBySliderCode($sliderCode)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.slider', 'sr', 'WITH', 'sr.code = :slider')
            ->setParameter('slider', $sliderCode)
            ->where('s.enabled = true')
            ->addOrderBy('s.position','ASC')
            ->getQuery()
            ->getResult();
    }
}
