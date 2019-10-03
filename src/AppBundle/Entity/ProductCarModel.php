<?php

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class ProductCarModel implements ResourceInterface
{
    /** @var int */
    private $id;

    /** @var Product */
    private $product;

    /** @var CarModel */
    private $carModel;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return CarModel
     */
    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    /**
     * @param CarModel $carModel
     */
    public function setCarModel(?CarModel $carModel): void
    {
        $this->carModel = $carModel;
    }
}
