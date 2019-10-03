<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\DropDown;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DropDownType
 * @package AppBundle\Form\Type
 */
class DropDownType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'none' => null,
                    'coding product' => 'coding',
                    'physical product' => 'physical',
                    'physical product with coding' => 'physical_coding',
                ]
            ])
            ->add('dropDownOptions', CollectionType::class, [
                'entry_type' => DropDownOptionType::class,
                'required'=>false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DropDown::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_drop_down';
    }
}
