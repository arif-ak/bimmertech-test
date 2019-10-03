<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderRefund;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OrderRefundAndReturnType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order_id', EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'id',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('order_item_unit_return', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => OrderItemUnit::class,
                ],
                'allow_add' => true
            ])
            ->add('comment', TextType::class);

        if ($options['is_create'] == true) {
            $builder
                ->add('order_item_unit_refund', CollectionType::class, [
                    'entry_type' => OrderItemUnitRefundType::class,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true
                ])
                ->add('percent', NumberType::class)
                ->add('total', IntegerType::class, [
                    'constraints' => [                        
                        new Regex(
                            [
                                'pattern' => '/^[0-9]\d*$/',
                                'message' => 'Please use only positive numbers.'
                            ]
                        ),
                    ]
                ]);
        }

        if ($options['is_create'] == false) {
            $builder
                ->add('order_refund_id', EntityType::class, [
                    'class' => OrderRefund::class,
                    'choice_label' => 'id',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'is_create' => true
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
