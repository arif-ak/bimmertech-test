<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

/**
 * Class ProductVariant
 * @package AppBundle\Entity
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    protected $product;

    /**
     * @var boolean
     */
    private $imageRequired = false;

    /**
     * @var string
     */
    private $instruction;

    /**
     * @var string
     */
    private $installationTime;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var integer
     */
    private $vincheckserviceId;

    /**
     * @var boolean
     */
    private $hasHardware = false;

    /**
     * @var boolean
     */
    private $hasSoftware = false;

    /**
     * @var \AppBundle\Entity\Warehouse
     */
    private $defaultWarehouse;

    /**
     * @var boolean
     */
    private $instructionRequired = false;

    /**
     * Set imageRequired
     *
     * @param boolean $imageRequired
     *
     * @return ProductVariant
     */
    public function setImageRequired($imageRequired): ?ProductVariant
    {
        $this->imageRequired = $imageRequired;

        return $this;
    }

    /**
     * Get imageRequired
     *
     * @return boolean
     */
    public function getImageRequired(): ?bool
    {
        return $this->imageRequired;
    }

    /**
     * Set instruction
     *
     * @param string $instruction
     *
     * @return ProductVariant
     */
    public function setInstruction($instruction): ?ProductVariant
    {
        $this->instruction = $instruction;

        return $this;
    }

    /**
     * Get instruction
     *
     * @return string
     */
    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    /**
     * Set installationTime
     *
     * @param string $installationTime
     *
     * @return ProductVariant
     */
    public function setInstallationTime($installationTime): ?ProductVariant
    {
        $this->installationTime = $installationTime;

        return $this;
    }

    /**
     * Get installationTime
     *
     * @return string
     */
    public function getInstallationTime(): ?string
    {
        return $this->installationTime;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return ProductVariant
     */
    public function setPriority($priority): ?ProductVariant
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set vincheckserviceId
     *
     * @param integer $vincheckserviceId
     *
     * @return ProductVariant
     */
    public function setVincheckserviceId($vincheckserviceId): ProductVariant
    {
        $this->vincheckserviceId = abs($vincheckserviceId);

        return $this;
    }

    /**
     * Get vincheckserviceId
     *
     * @return integer
     */
    public function getVincheckserviceId(): ?int
    {
        return $this->vincheckserviceId;
    }

    /**
     * Set hasHardware
     *
     * @param boolean $hasHardware
     *
     * @return ProductVariant
     */
    public function setHasHardware($hasHardware): ?ProductVariant
    {
        $this->hasHardware = $hasHardware;

        return $this;
    }

    /**
     * Get hasHardware
     *
     * @return boolean
     */
    public function getHasHardware(): ?bool
    {
        return $this->hasHardware;
    }

    /**
     * Set hasSoftware
     *
     * @param boolean $hasSoftware
     *
     * @return ProductVariant
     */
    public function setHasSoftware($hasSoftware): ?ProductVariant
    {
        $this->hasSoftware = $hasSoftware;

        return $this;
    }

    /**
     * Get hasSoftware
     *
     * @return boolean
     */
    public function getHasSoftware(): ?bool
    {
        return $this->hasSoftware;
    }

    /**
     * Set defaultWarehouse
     *
     * @param \AppBundle\Entity\Warehouse $defaultWarehouse
     *
     * @return ProductVariant
     */
    public function setDefaultWarehouse(Warehouse $defaultWarehouse = null): ?ProductVariant
    {
        $this->defaultWarehouse = $defaultWarehouse;

        return $this;
    }

    /**
     * Get defaultWarehouse
     *
     * @return Warehouse
     */
    public function getDefaultWarehouse(): ?Warehouse
    {
        return $this->defaultWarehouse;
    }

    /**
     * Get InstructionRequired
     *
     * @return bool
     */
    public function getInstructionRequired(): ?bool
    {
        return $this->instructionRequired;
    }

    /**
     * Set InstructionRequired
     *
     * @param $instructionRequired
     * @return mixed
     */
    public function setInstructionRequired($instructionRequired)
    {
        return $this->instructionRequired = $instructionRequired;
    }
}
