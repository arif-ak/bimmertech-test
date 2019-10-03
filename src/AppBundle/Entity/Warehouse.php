<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Warehouse
 *
 * @ORM\Table(name="warehouse")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WarehouseRepository")
 */
class Warehouse implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var TaxCategory
     */
    protected $taxCategory;

    /**
     * @var PaymentMethod
     */
    protected $paymentMethod;

    /**
     * @var Zone
     */
    protected $zone;

    /**
     * @var UserLogistic
     */
    private $userLogistic;

    /**
     * @var Collection
     */
    private $orders;

    /**
     * @var Collection
     */
    private $shippingMethod;

    /**
     * Warehouse constructor.
     */
    public function __construct()
    {
        $this->taxCategory = new ArrayCollection();
        $this->paymentMethod = new ArrayCollection();
        $this->shippingMethod = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Warehouse
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Warehouse
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Warehouse
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Warehouse
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Warehouse
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Warehouse
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add taxCategory
     *
     * @param TaxCategory $taxCategory
     *
     */
    public function addTaxCategory(TaxCategory $taxCategory)
    {
        if (true === $this->taxCategory->contains($taxCategory)) {
            return;
        }
        $this->taxCategory[] = $taxCategory;
        $taxCategory->addWarehouse($this);
    }

    /**
     * Remove taxCategory
     *
     * @param TaxCategory $taxCategory
     */
    public function removeTaxCategory(TaxCategory $taxCategory)
    {
        if (false === $this->taxCategory->contains($taxCategory)) {
            return;
        }
        $this->taxCategory->removeElement($taxCategory);
        $taxCategory->removeWarehouse($this);
    }

    /**
     * Get taxCategory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxCategory()
    {
        return $this->taxCategory;
    }

    /**
     * @param mixed taxCategory
     */
    public function setTaxCategory($taxCategory)
    {
        $this->taxCategory = $taxCategory;
    }

    /**
     * Add paymentMethod
     *
     * @param PaymentMethod $paymentMethod
     */
    public function addPaymentMethod(PaymentMethod $paymentMethod)
    {
//        if ($this->paymentMethod->contains($paymentMethod)) {
//            return;
//        }
        $this->paymentMethod[] = $paymentMethod;
        $paymentMethod->addWarehouse($this);
    }

    /**
     * Remove paymentMethod
     *
     * @param PaymentMethod $paymentMethod
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod)
    {
        if (!$this->paymentMethod->contains($paymentMethod)) {
            return;
        }
        $this->paymentMethod->removeElement($paymentMethod);
        $paymentMethod->removeWarehouse($this);
    }


    /**
     * Get paymentMethod
     *
     * @return ArrayCollection|PaymentMethod[]
     */
    public function getPaymentMethod()
    {
//        if (!$this->paymentMethod instanceof ArrayCollection) {
//            $this->paymentMethod = new ArrayCollection();
//        }
        return $this->paymentMethod;
    }

//
//    /**
//     * @param ArrayCollection $paymentMethod
//     * @return Warehouse
//     */
//    public function setPaymentMethod($paymentMethod)
//    {
//        $this->paymentMethod = $paymentMethod;
//        return $this;
//    }

    /**
     * @return mixed
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param mixed $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set userLogistic
     *
     * @param \AppBundle\Entity\UserLogistic $userLogistic
     *
     * @return Warehouse
     */
    public function setUserLogistic(\AppBundle\Entity\UserLogistic $userLogistic = null)
    {
        $this->userLogistic = $userLogistic;

        return $this;
    }

    /**
     * Get userLogistic
     *
     * @return \AppBundle\Entity\UserLogistic
     */
    public function getUserLogistic()
    {
        return $this->userLogistic;
    }

    /**
     * Add order
     *
     * @param Order $order
     *
     * @return Warehouse
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
     * Get orders
     *
     * @return Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add shippingMethod.
     *
     * @param ShippingMethod $shippingMethod
     *
     */
    public function addShippingMethod(ShippingMethod $shippingMethod)
    {
        if (true === $this->shippingMethod->contains($shippingMethod)) {
            return;
        }
        $this->shippingMethod[] = $shippingMethod;
        $shippingMethod->addWarehouse($this);
    }

    /**
     * Remove shippingMethod.
     *
     * @param ShippingMethod $shippingMethod
     *
     */
    public function removeShippingMethod(ShippingMethod $shippingMethod)
    {
        if (false === $this->shippingMethod->contains($shippingMethod)) {
            return;
        }
        $this->shippingMethod->removeElement($shippingMethod);
        $shippingMethod->removeWarehouse($this);
    }

    /**
     * Get shippingMethod.
     *
     * @return Collection
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }
}
