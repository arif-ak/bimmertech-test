<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectShippingTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('shipments', CollectionType::class, [
            'entry_type' => ShipmentType::class,
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'validation_groups' => 'app_address',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return SelectShippingType::class;
    }
}
