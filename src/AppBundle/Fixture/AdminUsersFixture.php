<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserMarketing;
use AppBundle\Entity\UserSale;
use AppBundle\Entity\UserSupport;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class AdminUsersFixture
 *
 * @package AppBundle\Fixture
 */
class AdminUsersFixture extends AbstractFixture
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * AdminUsersFixture constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $options = $options['custom'];

        foreach ($options as $item) {
            $user = $this->getUserFabric($item['role']);
            $user->setEmail($item['email']);
            $user->setUsername($item['username']);
            $user->setPlainPassword($item['password']);
            $user->setEnabled($item['enabled']);
            $user->setLocaleCode($item['locale_code']);
            $user->setFirstName($item['first_name']);
            $user->setLastName($item['last_name']);

            $this->objectManager->persist($user);
        }

        $this->objectManager->flush();
    }

    /**
     * Get User entity
     *
     * @param string $type
     *
     * @return AdminUser|UserCoding|UserLogistic|UserSale|UserSupport
     */
    private function getUserFabric(string $type)
    {
        switch ($type) {
            case 'admin':
                $user = new AdminUser;
                $user->addRole(AdminUser::DEFAULT_ADMIN_ROLE);

                return $user;

            case 'coding':
                $user = new UserCoding;
                $user->addRole(UserCoding::CODING_ROLE);

                return $user;

            case 'logistic':
                $user = new UserLogistic;
                $user->addRole(UserLogistic::LOGISTIC_ROLE);

                return $user;

            case 'sale':
                $user = new UserSale;
                $user->addRole(UserSale::SALE_ROLE);

                return $user;

            case 'support':
                $user = new UserSupport;
                $user->addRole(UserSupport::SUPPORT_ROLE);

                return $user;

            case 'marketing':
                $user = new UserMarketing;
                $user->addRole(UserMarketing::MARKETING_ROLE);

                return $user;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'admin_users';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('email')->cannotBeEmpty()->end()
            ->scalarNode('username')->cannotBeEmpty()->end()
            ->booleanNode('enabled')->end()
            ->booleanNode('api')->end()
            ->scalarNode('password')->cannotBeEmpty()->end()
            ->scalarNode('locale_code')->cannotBeEmpty()->end()
            ->scalarNode('first_name')->cannotBeEmpty()->end()
            ->scalarNode('last_name')->cannotBeEmpty()->end()
            ->scalarNode('role')->cannotBeEmpty()->end();
    }
}
