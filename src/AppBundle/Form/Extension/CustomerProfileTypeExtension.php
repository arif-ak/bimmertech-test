<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomerProfileTypeExtension
 * @package AppBundle\Form\Extension
 */
class CustomerProfileTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vatNumber', TextType::class, [
                'required' => false,
            ])
            ->add('vinNumber', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']])
                ]
            ])
            ->add('company', TextType::class, [
                'required' => false,
            ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return CustomerProfileType::class;
    }

}