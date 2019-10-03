<?php

namespace AppBundle\Form\Extension;

use AppBundle\Form\Type\OrderItemImageType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class OrderItemTypeExtension
 * @package AppBundle\Form\Extension
 */
class OrderItemTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $event->getForm()
                ->remove('quantity')
                ->add('images', CollectionType::class, [
                    'entry_type' => OrderItemImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Photo report',
                    'required' => false
                ]);
        });
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return OrderItemType::class;
    }
}