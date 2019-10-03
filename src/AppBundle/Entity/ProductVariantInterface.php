<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface as BaseProductVariantInterface;

/**
 * Interface ProductVariantInterface
 * @package AppBundle\Entity
 */
interface ProductVariantInterface extends BaseProductVariantInterface
{
    /**
     * @return ProductVariant
     */
    public function setImageRequired($imageRequired): ?ProductVariant;

    /**
     * @return boolean
     */
    public function getImageRequired(): ?bool;

    /**
     * @return ProductVariant
     */
    public function setInstruction($instruction): ?ProductVariant;

    /**
     * @return string
     */
    public function getInstruction(): ?string;

    /**
     * @return ProductVariant
     */
    public function setInstallationTime($installationTime): ?ProductVariant;

    /**
     * @return string
     */
    public function getInstallationTime(): ?string;

    /**
     * @return ProductVariant
     */
    public function setPriority($priority): ?ProductVariant;

    /**
     * @return integer
     */
    public function getPriority(): ?int;

    /**
     * @return ProductVariant
     */
    public function setVincheckserviceId($vincheckserviceId): ProductVariant;

    /**
     * @return integer
     */
    public function getVincheckserviceId(): ?int;

    /**
     * @return ProductVariant
     */
    public function setHasHardware($hasHardware): ?ProductVariant;

    /**
     * @return boolean
     */
    public function getHasHardware(): ?bool;

    /**
     * @return ProductVariant
     */
    public function setHasSoftware($hasSoftware): ?ProductVariant;

    /**
     * @return boolean
     */
    public function getHasSoftware(): ?bool;

    /**
     * @return ProductVariant
     */
    public function setDefaultWarehouse(Warehouse $defaultWarehouse = null): ?ProductVariant;

    /**
     * @return Warehouse
     */
    public function getDefaultWarehouse(): ?Warehouse;

}