<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserLogistic
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserLogisticRepository")
 */
class UserLogistic extends AdminUser implements UserLogisticInterface
{
    /**
     * @var Warehouse
     */
    protected $warehouse;

//    public function isLocked(): bool
//    {
//        return parent::isAccountNonLocked();
//    }

    /**
     * UserLogistic constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = [UserLogisticInterface::LOGISTIC_ROLE];
    }


    /**
     * Set warehouse
     *
     * @param \AppBundle\Entity\Warehouse $warehouse
     *
     * @return UserLogistic
     */
    public function setWarehouse(\AppBundle\Entity\Warehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;
        $this->roles[1] = strtoupper(UserLogisticInterface::LOGISTIC_ROLE . '_' . $warehouse->getCity());

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return \AppBundle\Entity\Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}
