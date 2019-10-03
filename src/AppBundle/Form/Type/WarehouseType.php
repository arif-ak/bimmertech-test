<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\ShippingMethod;
use AppBundle\Entity\TaxCategory;
use AppBundle\Entity\Warehouse;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WarehouseType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();
        $builder
            ->add('name')
            ->add('country', ChoiceType::class, array(
                'choices' => array_flip($countries),
                'label' => 'Country'
            ))
            ->add('city')
            ->add('zip')
            ->add('address')
            ->add('phone')
            ->add('zone')
            ->add('taxCategory', EntityType::class, [
                'class' => TaxCategory::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ])
            ->add('paymentMethod', EntityType::class, [
                'class' => PaymentMethod::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ])
            ->add('shippingMethod', EntityType::class, [
                'class' => ShippingMethod::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Warehouse::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_warehouse_type';
    }
}
