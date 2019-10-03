<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Product;
use AppBundle\Entity\Slide;
use AppBundle\Entity\Slider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('url')
            ->add('position')
            ->add('enabled')
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'code',
                'required' => false,
                'empty_data' => null
            ])
            ->add('slider', EntityType::class, [
                'class' => Slider::class,
                'choice_label' => 'name',
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => SlideImageType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'label' => 'sylius.form.slide.images',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slide::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_slide_type';
    }
}