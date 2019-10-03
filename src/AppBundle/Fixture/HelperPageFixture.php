<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\FaqHeader;
use AppBundle\Entity\FAQQuestion;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class HelperPageFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ContactTitle constructor.
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
        $helpers = $options['custom'];


        foreach ($helpers as $value) {
            $faqHeader = new FaqHeader();
            $faqHeader->setName($value['header']);
            $faqHeader->setCode($value['code']);
            $faqHeader->setPosition($value['positiion']);

            foreach ($value['helpers'] as $helper) {
                $faqHeader->addQuestions($this->addHelpers($helper));
            }
            $this->objectManager->persist($faqHeader);
        }

        $this->objectManager->flush();

    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'helper_page';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('header')->end()
            ->scalarNode('code')->end()
            ->scalarNode('positiion')->end()
            ->arrayNode('helpers')->prototype('array')
            ->children()
            ->scalarNode('code')->end()
            ->scalarNode('position')->end()
            ->scalarNode('enabled')->end()
            ->scalarNode('question')->end()
            ->scalarNode('answer')->end()
            ->end()
            ->end();
    }

    public function addHelpers( $helper){
        $faq = new FAQQuestion();
        $faq->setCode($helper['code']);
        $faq->setPosition($helper['position']);
        $faq->setEnabled($helper['enabled']);
        $faq->setQuestion($helper['question']);
        $faq->setAnswer($helper['answer']);
        $this->objectManager->persist($faq);

        $this->objectManager->flush();
        return $faq;
    }

}
