<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class CarModel
 * @package AppBundle\Entity
 */
class CarModel implements ResourceInterface, JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string;
     */
    private $year;

    /**
     * @var string;
     */
    private $model;

    /**
     * @var string;
     */
    private $series;

    /**
     * @var ArrayCollection
     */
    private $productCarModel;

    /**
     * CarModel constructor.
     */
    public function __construct()
    {
        $this->productCarModel = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return array|string
     */
    public function jsonSerialize ()
    {
        return[
            'name' => $this->title,
            'model'=> $this->code,
            'year'=> $this->year
        ];
    }

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return
     */
    public function getProductCarModel()
    {
        return $this->productCarModel;
    }

    /**
     * @param ProductCarModel $productCarModel
     */
    public function addProductCarModel(ProductCarModel $productCarModel)
    {
        if (true === $this->productCarModel->contains($productCarModel)) {
            return;
        }

        $this->productCarModel->add($productCarModel);
        $productCarModel->setCarModel($this);
    }

    /**
     * @param ProductCarModel $productCarModel
     */
    public function removeProductCarModel(ProductCarModel $productCarModel)
    {
        if (false === $this->productCarModel->contains($productCarModel)) {
            return;
        }

        $this->productCarModel->removeElement($productCarModel);
        $productCarModel->setCarModel(null);
    }

    /**
     * @return string
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string $series
     */
    public function setSeries(?string $series): void
    {
        $this->series = $series;
    }
}
