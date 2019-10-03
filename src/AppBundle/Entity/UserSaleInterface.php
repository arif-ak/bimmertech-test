<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;

/**
 * Interface UserSaleInterface
 * @package AppBundle\Entity
 */
interface UserSaleInterface extends AdminUserInterface
{
    public const SALE_ROLE = 'ROLE_SALE_ACCESS';
}