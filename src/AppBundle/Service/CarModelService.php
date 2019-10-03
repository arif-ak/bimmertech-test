<?php

namespace AppBundle\Service;

use AppBundle\Entity\CarModel;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCarModel;
use Doctrine\ORM\EntityManager;

class CarModelService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * CarModelService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function carModelProduct(?Product $product)
    {
        $carModels = $product->getProductCarModel();

        $productCarModelsArray = [];
        /** @var ProductCarModel $productCarModel */
        foreach ($carModels as $productCarModel) {
            /** @var CarModel $model */
            $carModel = $productCarModel->getCarModel();
            $series = $carModel->getSeries();
            $productCarModelsArray[$series][] = $carModel->getModel();
        }

        return $productCarModelsArray;
    }

    public function carModelProductContainer($products)
    {
        $productCarModelsArray = [];
        foreach ($products as $product) {
            $carModels = $product->getProductCarModel();

            /** @var ProductCarModel $productCarModel */
            foreach ($carModels as $key => $productCarModel) {
                /** @var CarModel $model */
                $carModel = $productCarModel->getCarModel();
                $series = $carModel->getSeries();

                $productCarModelsArray[$series][$carModel->getId()] = $carModel->getModel();
            }
        }

        return $productCarModelsArray;
    }
}
