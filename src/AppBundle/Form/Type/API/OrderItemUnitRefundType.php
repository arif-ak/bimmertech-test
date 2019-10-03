<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\ContactContent;
use AppBundle\Entity\OrderItemUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OrderItemUnitRefundType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_item_unit', EntityType::class, [
                'class' => OrderItemUnit::class,
                'choice_label' => 'id',
            ])
            ->add('value', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex(
                        [
                            'pattern' => '/^[0-9]\d*$/',
                            'message' => 'Please use only positive numbers.'
                        ]
                    ),
                ]
            ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
