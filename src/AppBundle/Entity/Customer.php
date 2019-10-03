<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\Customer as BaseCustomer;

class Customer extends BaseCustomer
{
    /**
     * @var
     */
    protected $userSale;

    /**
     * @var string
     */
    protected $vatNumber;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $vinNumber;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * Set userSale
     *
     * @param \AppBundle\Entity\UserSale $userSale
     *
     * @return Customer
     */
    public function setUserSale(\AppBundle\Entity\UserSale $userSale = null)
    {
        $this->userSale = $userSale;

        return $this;
    }

    /**
     * Get userSale
     *
     * @return \AppBundle\Entity\UserSale
     */
    public function getUserSale()
    {
        return $this->userSale;
    }

    /**
     * Add order
     *
     * @param Order $order
     *
     * @return Customer
     */
    public function addOrder(Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param Order $order
     */
    public function removeOrder(Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get vat number
     *
     * @return string
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * Set vat number
     *
     * @param string $vatNumber
     * @return string
     */
    public function setVatNumber($vatNumber): ?string
    {
        return $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getVinNumber(): ?string
    {
        return $this->vinNumber;
    }

    /**
     * @param string $vinNumber
     */
    public function setVinNumber( $vinNumber): void
    {
        $this->vinNumber = $vinNumber;
    }
}
