<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BuyersGuideImage;
use AppBundle\Service\ImageUploader;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class PhotoReportType
 * @package AppBundle\Form\Type
 */
class BuyersGuideImageType extends ImageType
{
    /**
     * @var ImageUploader
     */
    protected $uploader;

    protected $repository;

    /**
     * ProductPropertiesType constructor.
     * @param ImageUploader $uploader
     */
    public function __construct(ImageUploader $uploader, $repository)
    {
        parent::__construct(BuyersGuideImage::class);
        $this->uploader = $uploader;
        $this->repository = $repository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => false,
        ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $file = $data->getFile();
                if ($file) {
                    $this->uploader->upload($data);
                }

                // Upload failed? Let's remove that image.
                if (null === $data->getPath()) {
                }
            });
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'photo_report_image';
    }
}
