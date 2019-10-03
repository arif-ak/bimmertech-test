<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order_item_id', EntityType::class, [
                'class' => OrderItem::class,
                'choice_label' => 'id',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('instruction', TextType::class);
//            ->add('status', TextType::class, [
//                'constraints' => [
//                    new NotBlank(),
//                    new Choice([
//                        OrderItemInterface::COMPLETE,
//                        OrderItemInterface::NOT_SEND,
//                        OrderItemInterface::NOT_ADDED
//                    ])
//                ],
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
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
