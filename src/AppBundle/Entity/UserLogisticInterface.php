<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;

/**
 * Interface UserLogisticInterface
 * @package AppBundle\Entity
 */
interface UserLogisticInterface extends AdminUserInterface
{
    public const LOGISTIC_ROLE = 'ROLE_LOGISTIC_ACCESS';
}