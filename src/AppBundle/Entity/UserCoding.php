<?php

namespace AppBundle\Entity;

/**
 * Class UserCoding
 * @package AppBundle\Entity
 */
class UserCoding extends AdminUser implements UserCodingInterface
{
    /**
     * UserCoding constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->roles = [UserCodingInterface::CODING_ROLE];
    }
}