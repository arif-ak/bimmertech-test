<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Customer;
use AppBundle\Entity\UserSale;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSaleType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('plainPassword')
            ->add('enabled')
            ->add('localeCode', LocaleType::class, [
                'placeholder' => null,
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false

            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSale::class,
            'allow_extra_fields' => true

        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_user_sale_type';
    }
}
