<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\ShopUser as BaseShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;

class ShopUser extends BaseShopUser implements ShopUserInterface
{
    protected $path;

    protected $isClosedAccount;

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
     * @return mixed
     */
    public function getisClosedAccount()
    {
        return $this->isClosedAccount;
    }

    /**
     * @param mixed $isClosedAccount
     */
    public function setIsClosedAccount($isClosedAccount): void
    {
        $this->isClosedAccount = $isClosedAccount;
    }
}
