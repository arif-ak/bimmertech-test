<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ContactContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactContentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Name',
            ])
            ->add('value', TextareaType::class, [
                'label' => 'Value',
            ])
            ->add('hours', TextType::class, [
                'label' => 'Hours',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactContent::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_contact_content_type';
    }
}
