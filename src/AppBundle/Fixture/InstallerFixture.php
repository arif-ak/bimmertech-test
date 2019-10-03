<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.02.2019
 * Time: 13:44
 */

namespace AppBundle\Fixture;

use AppBundle\Entity\Installer;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class InstallerFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * InstallerFixture constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $installers = $options['custom'];

        foreach ($installers as $item){

            $installer = new Installer();

            $installer->setName($item['name']);
            $installer->setAddress($item['address']);
            $installer->setType($item['type']);
            $installer->setLatitude($item['latitude']);
            $installer->setLongitude($item['longitude']);
            $installer->setLink($item['link']);

            $this->objectManager->persist($installer);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'installer';
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('address')->end()
            ->scalarNode('type')->end()
            ->scalarNode('latitude')->end()
            ->scalarNode('longitude')->end()
            ->scalarNode('link')->end()
            ->end();
    }
}