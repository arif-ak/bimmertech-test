<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;

/**
 * Interface UserCodingInterface
 * @package AppBundle\Entity
 */
interface UserCodingInterface extends AdminUserInterface
{
    public const CODING_ROLE = 'ROLE_CODING_ACCESS';
}
