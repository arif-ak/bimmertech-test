<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Warehouse;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserLogisticType
 * @package AppBundle\Form\Type
 */
class UserLogisticType extends AbstractResourceType
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
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
//                'by_reference' => false
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_user_logistic_type';
    }
}
