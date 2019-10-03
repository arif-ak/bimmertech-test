<?php

namespace AppBundle\Entity;

/**
 * Class AddressOverride
 * @package AppBundle\Entity
 */
class Address extends AddressOverride
{
    /**
     * {@inheritdoc}
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode(?string $countryCode): void
    {
        if (null === $countryCode) {
            $this->provinceCode = null;
        }

        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvinceCode(?string $provinceCode): void
    {
        if (null === $this->countryCode) {
            return;
        }

        $this->provinceCode = $provinceCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvinceName(): ?string
    {
        return $this->provinceName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvinceName(?string $provinceName): void
    {
        $this->provinceName = $provinceName;
    }
}
