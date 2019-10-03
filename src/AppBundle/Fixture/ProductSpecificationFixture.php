<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.03.2019
 * Time: 13:40
 */

namespace AppBundle\Fixture;


use AppBundle\Entity\ProductAttribute;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;

class ProductSpecificationFixture extends AbstractFixture implements FixtureInterface
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


    public function load(array $options): void
    {
        dump(4225);
        $specifications = $options['custom'];

        foreach ($specifications as $attribute){

            $specification = new ProductAttribute();
            $specification->setCode($attribute['code']);
            $specification->setType($attribute['type']);
            $specification->setStorageType($attribute['storage_type']);
            $specification->setPosition($attribute['position']);
            $specification->setName($attribute['name']);


            $this->objectManager->persist($specification);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'product_specification';
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
            ->scalarNode('storage_type')->end()
            ->scalarNode('position')->end()
            ->end();
    }

//    public function setTranslation($b,$attribute){
//        $translation = new ProductAttributeTranslation();
//        $translation->setName($attribute['name']);
//        $translation->setLocale('en_US');
//
//        $this->objectManager->persist($translation);
//        $this->objectManager->flush();
//        return $translation;
//
//    }
}