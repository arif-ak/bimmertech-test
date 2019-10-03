<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AppUSPSGatewayType
 * @package AppBundle\Form\Type
 */
final class AppUSPSGatewayType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => [
                    'Staging' => true,
                    'Production' => false,
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'USPS username',
                'constraints' => [
                    new NotBlank([
                        'message' => 'USPS username cannot be blank.',
                        'groups' => 'bitbag',
                    ])
                ],
            ])
            ->add('apiVersion', ChoiceType::class, [
                'label' => 'Api Version',
                'choices' => [
                    'Express Mail Intl' => 'ExpressMailIntl',
                    'Priority Mail Intl' => 'PriorityMailIntl',
                    'First Class Mail Intl' => 'FirstClassMailIntl',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
            ])
            ->add('address', TextType::class, [
                'label' => 'Addresss',
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'sylius.form.address.country',
                'enabled' => true
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'bitbag.ui.postal_code',
            ])
            ->add('address2', TextType::class, [
                'label' => 'Phone number',
            ]);
    }
}