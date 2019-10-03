<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Home;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageType
 *
 * @package AppBundle\Form\Type
 */
class HomeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('subTitle')
            ->add('slug', TextType::class, [
                'attr' => ['disabled' => 'disabled'],
                'data' => 'devtestshop.bimmer-tech.net'
            ])
            ->add('metaTitle')
            ->add('metaDescription', TextareaType::class)
            ->add('metaKeywords', TextType::class)
            ->add('description', CKEditorType::class, [
                'label' => "Description"
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Home::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_home';
    }
}
