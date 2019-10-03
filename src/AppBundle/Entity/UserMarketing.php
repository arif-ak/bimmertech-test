<?php

namespace AppBundle\Entity;

/**
 * Class UserMarketing
 *
 * @package AppBundle\Entity
 */
class UserMarketing extends AdminUser implements UserMarketingInterface
{
    /**
     * UserMarketing constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = [UserMarketingInterface::MARKETING_ROLE];
    }
}
