<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.02.2019
 * Time: 13:44
 */

namespace AppBundle\Fixture;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class DropDownFixture extends AbstractFixture implements FixtureInterface
{

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * PageFixture constructor.
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
        $dropDowns = $options['custom'];

        foreach ($dropDowns as $key => $item) {

            $dropDown = $this->objectManager->getRepository(DropDown::class)->findOneBy(['code' => $item['code']]);

            if (null === $dropDown ) {
                $dropDown = new DropDown();
            }
            $dropDown->setCode($item['code']);
            $dropDown->setName($item['name']);
            $dropDown->setType($item['type']);



            foreach ($item['dropdown_options'] as $option){

                $dropDown->addDropDownOption($this->addOptions($option));
                $this->objectManager->persist($dropDown);
            }

        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'drop_down';
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
            ->scalarNode('code')->end()
            ->scalarNode('name')->end()
            ->scalarNode('type')->end()
            ->arrayNode('dropdown_options')->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('price')->end()
            ->scalarNode('position')->end()
            ->end()
            ->end();
    }

    public function addOptions(array $option){
        $dropDownOption = new DropDownOption();
        $dropDownOption->setName($option['name']);
        $dropDownOption->setPrice($option['price']);
        $dropDownOption->setPosition($option['position']);

        $this->objectManager->persist($dropDownOption);
        $this->objectManager->flush();

        return $dropDownOption;
    }
}