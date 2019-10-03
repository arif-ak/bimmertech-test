<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;

/**
 * Interface UserSupportInterface
 * @package AppBundle\Entity
 */
interface UserSupportInterface extends AdminUserInterface
{
    public const SUPPORT_ROLE = 'ROLE_SUPPORT_ACCESS';
}