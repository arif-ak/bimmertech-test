<?php

namespace AppBundle\Repository;

use AppBundle\Entity\FAQQuestion;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


class FAQQuestionRepository extends EntityRepository
{
    /**
     * @param $search
     *
     * @return array
     */
    public function sortByHeader($search = null): array
    {
        $faq = $this->createQueryBuilder('f');

        if ($search) {
            $faq->where('f.question LIKE :searchQ');
            $faq->orWhere('f.answer LIKE :searchA');
            $faq->setParameter('searchQ', '%' . $search . '%');
            $faq->setParameter('searchA', '%' . $search . '%');
        }

        $faq->andWhere('f.enabled = true')
            ->addOrderBy('f.header', 'ASC')
            ->addOrderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $faq->getQuery()
            ->getResult();
    }

    public function getForSorting(FAQQuestion $entity)
    {
        return $this->createQueryBuilder('f')
            ->where('f.id != :id')
            ->andWhere('f.header = :header')
            ->setParameter('id', $entity->getId())
            ->setParameter('header', $entity->getHeader())
            ->orderBy('f.position')
            ->getQuery()
            ->getResult();
    }
}
