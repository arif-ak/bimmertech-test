<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Taxon;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Model\TaxonImage;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TaxonFixture implements FixtureInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var TaxonSlugGeneratorInterface
     */
    private $taxonSlugGenerator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * TaxonFixture constructor.
     * @param TaxonRepositoryInterface $taxonRepository
     * @param RepositoryInterface $localeRepository
     * @param TaxonSlugGeneratorInterface $taxonSlugGenerator
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        RepositoryInterface $localeRepository,
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        ObjectManager $objectManager,
        ImageUploaderInterface $imageUploader
    )
    {
        $this->taxonRepository = $taxonRepository;
        $this->localeRepository = $localeRepository;
        $this->taxonSlugGenerator = $taxonSlugGenerator;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('custom', [])
                ->setAllowedTypes('custom', 'array');
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        foreach ($options['custom'] as $resourceOptions) {
            $resource = $this->create($resourceOptions);
            $this->objectManager->persist($resource);
        }

        $this->objectManager->flush();
        $this->objectManager->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): TaxonInterface
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $options['code']]);

        if (null === $taxon) {
            $taxon = new Taxon();
        }

        $taxon->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $taxon->setCurrentLocale($localeCode);
            $taxon->setFallbackLocale($localeCode);

            $taxon->setName($options['name']);
            $taxon->setSlug($options['slug'] ?: $this->taxonSlugGenerator->generate($taxon, $localeCode));

            if (isset($options['description'])) {
                $taxon->setDescription($options['description']);
            }

            if (isset($options['productName'])) {
                $taxon->setProductName($options['productName']);
            }

            if (isset($options['price'])) {
                $taxon->setPrice($options['price']);
            }

            if (isset($options['shortDescription'])) {
                $taxon->setShortDescription($options['shortDescription']);
            }

            if (isset($options['image'])) {
                $taxon = $this->createImages($taxon, $options['image']);
            }

            if (isset($options['isContainer'])) {
                $taxon->setIsContainer($options['isContainer']);
            }

        }

        if (isset($options['children'])) {
            foreach ($options['children'] as $childOptions) {
                $taxon->addChild($this->create($childOptions));
            }
        }
        return $taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'taxons';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->scalarNode('slug')->cannotBeEmpty()->end()
            ->scalarNode('description')->cannotBeEmpty()->end()
            ->scalarNode('image')->end()
            ->booleanNode('isContainer')->end()
            ->variableNode('children')->cannotBeEmpty()->defaultValue([])->end();
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $optionsNode = $treeBuilder->root($this->getName());
        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode('custom');
        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->prototype('array');
        $this->configureResourceNode($resourceNode);

        return $treeBuilder;
    }

    /**
     * @return iterable
     */
    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * @param Taxon $taxon
     * @param $image
     * @return Taxon
     */
    private function createImages(Taxon $taxon, $image)
    {
        $uploadedImage = new UploadedFile(__DIR__ . '/../../..' . $image, basename($image));

        $taxonImage = new TaxonImage();
        $taxonImage->setFile($uploadedImage);
        $this->imageUploader->upload($taxonImage);
        $taxon->addImage($taxonImage);

        return $taxon;
    }
}