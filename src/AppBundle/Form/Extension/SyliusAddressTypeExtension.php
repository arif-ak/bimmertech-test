<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SyliusAddressTypeExtension
 * @package AppBundle\Form\Extension
 */
final class SyliusAddressTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder->remove('firstName');
//        $builder->remove('lastName');
        $builder->add('phoneNumber', TextType::class, [
            'required' => true
        ]);
//        $builder->remove('company');


//        $builder->remove('street');
//        $builder->remove('city');
//        $builder->remove('postcode');
//        $builder->remove('countryCode');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
//        $resolver->setDefaults(array(
//            'validation_groups' => false,
//        ));
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return AddressType::class;
    }
}
