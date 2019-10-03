<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;

class AdminUser extends BaseAdminUser
{
    /**
     * @var History
     */
    private $history;

    /**
     * Remove oauthAccount
     *
     * @param \Sylius\Component\User\Model\UserOAuth $oauthAccount
     */
    public function removeOauthAccount(\Sylius\Component\User\Model\UserOAuth $oauthAccount)
    {
        $this->oauthAccounts->removeElement($oauthAccount);
    }
}
