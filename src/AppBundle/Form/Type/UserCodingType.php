<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserCodingType
 * @package AppBundle\Form\Type
 */
class UserCodingType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_user_coding_type';
    }
}