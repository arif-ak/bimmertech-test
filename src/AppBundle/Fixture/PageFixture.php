<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Page;
use AppBundle\Entity\PageDescription;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PageFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * PageFixture constructor.
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(ObjectManager $objectManager, ImageUploaderInterface $imageUploader)
    {
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $pages = $options['custom'];

        foreach ($pages as $key => $item) {
            $page = $this->objectManager->getRepository(Page::class)->findOneBy(['slug' => $item['slug']]);

            if (null === $page) {
                $page = new Page();
            }else{
                continue;
            }

            //$page = new Page();

            $page->setTitle($item['title']);
            $page->setSlug($item['slug']);
            $page->addPageDescription($this->addPageDescriptions($item['description']));

            $this->objectManager->persist($page);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'about_us_page';
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
            ->scalarNode('title')->end()
            ->scalarNode('slug')->end()
            ->arrayNode('description')->prototype('array')
            ->children()
            ->scalarNode('header')->end()
            ->scalarNode('sub_header')->end()
            ->scalarNode('content')->end()
            ->scalarNode('video')->end()
            ->scalarNode('type')->end()
            ->end();
    }

    /**
     * @param array $description
     * @return PageDescription
     */
    public function addPageDescriptions(array $description)
    {

        $pageDescription = new PageDescription();

        foreach ($description as $item) {

            $pageDescription->setHeader($item['header']);
            $pageDescription->setSubHeader($item['sub_header']);
            $pageDescription->setContent($item['content']);
            $pageDescription->setVideo('video');
            $pageDescription->setType('type');
            $pageDescription = $this->createImage($pageDescription, $image = '/fixture_images/product_installer/no_image_installer.png');

            $this->objectManager->persist($pageDescription);
            $this->objectManager->flush();
        }

        return $pageDescription;
    }

    /**
     * @param PageDescription $pageDescription
     * @param $image
     * @return PageDescription
     */
    private function createImage(PageDescription $pageDescription, $image)
    {
        $file = new UploadedFile(__DIR__ . '/../../..' . $image, basename($image));

        $image = $pageDescription->getImages()->first();
        $image->setFile($file);
        $this->imageUploader->upload($image);

        return $pageDescription;
    }
}