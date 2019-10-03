<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PhotoReportType
 * @package AppBundle\Form\Type
 */
class PhotoReportType extends ImageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => false,
            'attr' => array('accept' => 'image/png,image/jpeg')
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'photo_report_image';
    }
}
