<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Slide;
use AppBundle\Entity\Slider;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SliderFixture
 * @package AppBundle\Fixture
 */
class SliderFixture extends AbstractFixture implements FixtureInterface
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ImageUploaderInterface
     */
    protected $imageUploader;

    /**
     * SliderFixture constructor.
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
        $slider = $this->createSlider($options);
        $i = 1;

        foreach ($options['slides'] as $item) {

            $slide = new Slide;

            $slide->setCode('slide_' . $i++);
            $slide->setName('slide ' . $i++);
            $slide->setEnabled(true);
            $slide->setPosition($i);
            $slide->setSlider($slider);
            $slide->setUrl('#');
            $slide->setDescription($options['description']);
            $slide->setPrice('100');

            $slide = $this->createImages($slide, $item);
            $this->objectManager->persist($slide);
        }
        $this->objectManager->flush();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'slider';
    }

    /**
     * @param array $options
     * @return Slider
     */
    private function createSlider(array $options)
    {
        $slider = new Slider();

        $slider->setCode($options['code']);
        $slider->setName($options['name']);
        $this->objectManager->persist($slider);
        $this->objectManager->flush();

        return $slider;
    }

    /**
     * @param ArrayNodeDefinition $optionsNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->scalarNode('description')->cannotBeEmpty()->end()
            ->arrayNode('slides')->prototype('scalar')->end()->end();
    }

    /**
     * @param Slide $slide
     * @param $image
     * @return Slide
     */
    private function createImages(Slide $slide, $image)
    {
        $uploadedImage = new UploadedFile(__DIR__.'/../../..'. $image, basename($image));

        $slideImage = $slide->getImages()->first();
        $slideImage->setFile($uploadedImage);
        $this->imageUploader->upload($slideImage);

        return $slide;
    }
}