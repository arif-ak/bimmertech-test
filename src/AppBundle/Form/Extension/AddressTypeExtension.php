<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressTypeExtension
 * @package AppBundle\Form\Extension
 */
final class AddressTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $event->getForm()
                ->add('notes', TextareaType::class, [
                    'required' => false,
                    'label' => 'Comments',
                ])
                ->add('vin', TextType::class, [
                    'required' => true,
                    'label' => 'Vehicle Identification Number (VIN)',
                ])
                ->add('countryCode', CountryCodeChoiceType::class, [
                    'label' => 'sylius.form.address.country',
                    'enabled' => true
                ])
                ->add('userSale', null, [
                    'label' => 'Who was your sales rep?',
                ])
//                ->remove('billingAddress')
//                ->remove('shippingAddress')
                ->remove('differentBillingAddress');
        });
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'validation_groups' => 'app_address',
        ));
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return AddressType::class;
    }
}