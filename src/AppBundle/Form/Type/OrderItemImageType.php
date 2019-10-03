<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrderItemImageType
 * @package AppBundle\Form\Type
 */
class OrderItemImageType extends ImageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => 'sylius.form.image.file',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'app_order_item_image';
    }
}
