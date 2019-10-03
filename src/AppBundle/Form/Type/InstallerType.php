<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Installer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstallerType extends AbstractType
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
            ->add('address', TextareaType::class, [
                'label' => 'Address',
            ])
            ->add('link', TextType::class, [
                'label' => 'Link',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Platinum' => Installer::PLATINUM_TYPE,
                    'Gold' => Installer::GOLD_TYPE,
                    'Silver' => Installer::SILVER_TYPE,
                    'Bronze' => Installer::BRONZE_TYPE
                ],
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Installer::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_installer_type';
    }
}
