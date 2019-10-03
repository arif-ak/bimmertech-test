<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\Order;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * Class AdminUserService
 * @package AppBundle\Service
 */
class AdminUserService
{
    /**
     * @var SenderInterface
     */
    private $sender;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * AdminUserService constructor.
     * @param $repository
     * @param SenderInterface $sender
     */
    public function __construct(EntityRepository $repository, SenderInterface $sender)
    {
        $this->repository = $repository;
        $this->sender = $sender;
    }

    /**
     * @param array $userRoles
     * @return array
     */
    private function getEmailUserByRole(array $userRoles)
    {
        $users = $this->repository->findAll();

        $usersEmail = [];
        foreach ($users as $user) {
            /**
             * @var AdminUser $user
             */
            $roles = $user->getRoles();

            foreach ($roles as $role) {
                foreach ($userRoles as $userRole) {
                    if ($userRole == $role) {
                        $usersEmail[] = $user->getEmail();
                    }
                }
            }
        }
        return $usersEmail;
    }

    /**
     * @param array $userRoles
     * @param $twigTemplate
     * @param $order
     */
    public function sendEmail(array $userRoles, $twigTemplate, $data)
    {
        $usersEmail = $this->getEmailUserByRole($userRoles);
        $this->sender->send($twigTemplate, $usersEmail, ['data' => $data]);
    }
}