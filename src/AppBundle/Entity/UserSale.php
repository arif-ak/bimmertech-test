<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Class UserSale
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserSaleRepository")
 */
class UserSale extends AdminUser implements UserSaleInterface
{
    /**
     * @var ArrayCollection
     */
    protected $customers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orders;

    public function isLocked(): bool
    {
        return parent::isAccountNonLocked();
    }

    /**
     * UserSale constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = [UserSaleInterface::SALE_ROLE];
        $this->customers = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    /**
     * Add customers
     *
     * @param \AppBundle\Entity\Customer $customers
     *
     * @return void
     */
    public function addCustomer(\AppBundle\Entity\Customer $customers)
    {
        if ($this->customers->contains($customers)) {
            return;
        }
        $this->customers[] = $customers;
        $customers->setUserSale($this);
    }

    /**
     * Remove customers
     *
     * @param \AppBundle\Entity\Customer $customers
     */
    public function removeCustomer(\AppBundle\Entity\Customer $customers)
    {
        $this->customers->removeElement($customers);
        $customers->setUserSale(null);
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomer()
    {
        return $this->customers;
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            return (string)($this->firstName . ' ' . $this->lastName);
        } catch (Exception $exception) {
            return '';
        }
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Customer $order
     *
     * @return UserSale
     */
    public function addOrder(\AppBundle\Entity\Customer $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Customer $order
     */
    public function removeOrder(\AppBundle\Entity\Customer $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
