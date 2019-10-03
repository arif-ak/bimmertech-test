<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShippingMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class ShipmentPrepareLabelType
 * @package AppBundle\Form\Type
 */
class ShipmentPrepareLabelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tracking_number', TextType::class, [
                'label' => '',
                'required' => false,
            ])
            ->add('courier', TextType::class, [
                'label' => '',
                'required' => false,
            ])
            ->add('ship_method_id', EntityType::class, [
                'class' => ShippingMethod::class,
                'choice_label' => 'id',
            ])

            ->add('images', CollectionType::class, [
                'entry_type' => FileType::class,
                'allow_add' => true
            ])
            ->add('order_id', EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'id',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        if (!$options['foreignShipment']) {
            $builder
                ->add('dhl_weight', TextType::class)
                ->add('insured_amount', NumberType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
                ->add('number_of_pieces', NumberType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
                ->add('width', NumberType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
                ->add('height', NumberType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ]
                ])
                ->add('length', NumberType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]);
        }

        if ($options['shipment']) {
            $builder->add('shipment_id', EntityType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'class' => Shipment::class,
                'choice_label' => 'id',
            ])
            ->add('removed_image_ids', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true
            ]);
        }

        if ($options['isCreate']) {
            $builder->add('order_item_units', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => OrderItemUnit::class,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'allow_add' => true
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'shipment' => false,
            'foreignShipment' => false,
            'isCreate' => true
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
