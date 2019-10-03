<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\OrderItemUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WarehouseShipmentType
 * @package AppBundle\Form\Type
 */
class WarehouseShipmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('units', EntityType::class, array(
                'class' => OrderItemUnit::class,
                'choices' => $options['orderUnits'],
                'choice_label' => 'orderItem.productName',
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'multiple' => true,
                    'class' => 'ui fluid dropdown'
                ],
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_warehouse_shipment';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'orderUnits' => [],
        ));
    }
}
