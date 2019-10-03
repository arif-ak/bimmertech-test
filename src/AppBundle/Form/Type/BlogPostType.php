<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\BlogPost;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuyersGuideOptionType
 * @package AppBundle\Form\Type
 */
class BlogPostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class)
            ->add('title', TextType::class, [
                'required' => false
            ])
            ->add('blogCategory', EntityType::class, [
                'class' => BlogCategory::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Enabled',
                'data' => true,
            ])
            ->add('metaKeywords', TextType::class, [
                'required' => false
            ])
            ->add('metaDescription', TextType::class, [
                'required' => false
            ])
            ->add('metaTags', TextType::class, [
                'required' => false
            ])
            ->add('blogPostImage', CollectionType::class, [
                'entry_type' => BlogPostImageType::class,

                'by_reference' => false,
                'prototype' => true,
                'label' => 'Thumbnail item'
            ])
            ->add('content', TextType::class, [
                'required' => false
            ])
            ->add('productRelated', CollectionType::class, [
                'entry_type' => BlogPostProductType::class,
                'allow_add' => true,
                'required' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Interested in item'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'blog_post';
    }
}
