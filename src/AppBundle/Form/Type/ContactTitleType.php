<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ContactTitle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactTitleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('popup', ChoiceType::class, [
                'label' => 'Popup',
                'choices' => [
                    'None' => ContactTitle::NONE,
                    'Sales' => ContactTitle::SALES,
                    'Support' => ContactTitle::SUPPORT,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactTitle::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_contact_title_type';
    }
}
