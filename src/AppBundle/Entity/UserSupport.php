<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserSupport
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserSupportRepository")
 */
class UserSupport extends AdminUser implements UserSupportInterface
{
    protected $refundAccess;

    /**
     * @return mixed
     */
    public function getRefundAccess()
    {
        return $this->refundAccess;
    }

    /**
     * @param mixed $refundAccess
     */
    public function setRefundAccess($refundAccess): void
    {
        $this->refundAccess = $refundAccess;
    }

    public function isLocked(): bool
    {
        return parent::isAccountNonLocked();
    }

    /**
     * UserSupport constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = [UserSupportInterface::SUPPORT_ROLE];
    }
}
