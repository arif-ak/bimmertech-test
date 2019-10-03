<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SelectPaymentTypeExtension
 * @package AppBundle\Form\Extension
 */
final class SelectPaymentTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    }

    /**
     * @param OptionsResolver $resolver
     */
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
        return SelectPaymentType::class;
    }
}
