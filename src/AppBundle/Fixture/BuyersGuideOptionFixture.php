<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\BuyersGuideOption;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class BuyersGuideOptionFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Warehouse constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @{@inheritdoc}
     */
    public function load(array $options): void
    {
        foreach ($this->getData() as $key => $item) {
            $carModel = new BuyersGuideOption();
            $carModel->setName($item);

            $this->objectManager->persist($carModel);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'buyers_guide_option';
    }

    private function getData()
    {
        return
            [
                'No option',
                'Manufacture',
                'Camera mode',
                'Requirements',
                'Features',
                'Coding',
                'Vehicle integration',
                'Installation time',
                'Compatible add-ons',
                'Price'
            ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->scalarNode('load')->cannotBeEmpty()->end();
    }
}
