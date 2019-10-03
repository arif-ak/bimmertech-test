<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\UserSupport;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSupportType extends AbstractResourceType
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
            ->add('refundAccess')
            ->add('localeCode', LocaleType::class, [
                'placeholder' => null,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSupport::class,
            'allow_extra_fields' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_user_support_type';
    }
}
