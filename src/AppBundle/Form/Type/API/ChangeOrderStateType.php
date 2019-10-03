<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\Shipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeOrderStateType extends AbstractType
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
                ->add('state_variable', TextType::class, [
                    'constraints' => [
                        new Choice([
//                        OrderInterface::ORDER_CODDING_STATE,
                            OrderInterface::ORDER_SHIPMENT_STATE,
//                        OrderInterface::ORDER_PAYMENT_STATE,
//                        OrderInterface::ORDER_SUPPORT_STATE,
                            OrderInterface::ORDER_GENERAL_STATE
                        ]),
                    ],
                ]);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                if ($form->get('state_variable')) {
                    $stateVariable = $form->get('state_variable')->getData();
                }

//            if ($stateVariable == OrderInterface::ORDER_SUPPORT_STATE) {
//                $form->add('value', TextType::class, [
//                    'constraints' => [
//                        new NotBlank(),
//                        new Choice([
//                            OrderItemInterface::NOT_ADDED,
//                            OrderItemInterface::PARTIALLY_ADDED,
//                            OrderItemInterface::NOT_REQUIRED,
//                            OrderItemInterface::COMPLETE,
//                        ]),
//                    ]]);
//            } elseif ($stateVariable == OrderInterface::ORDER_PAYMENT_STATE) {
//                $form->add('value', TextType::class, [
//                    'constraints' => [
//                        new NotBlank(),
//                        new Choice([
//                            PaymentInterface::STATE_CANCELLED,
//                            PaymentInterface::STATE_REFUNDED,
//                            PaymentInterface::STATE_PARTIALLY_REFUNDED,
//                            PaymentInterface::STATE_PAID
//                        ]),
//                    ]]);
//            } elseif ($stateVariable == OrderInterface::ORDER_CODDING_STATE) {
//                $form->add('value', TextType::class, [
//                    'constraints' => [
//                        new NotBlank(),
//                        new Choice([
//                            OrderItemInterface::NOT_CODED,
//                            OrderItemInterface::PARTIALLY_CODED,
//                            OrderItemInterface::NOT_REQUIRED,
//                            OrderItemInterface::COMPLETE,
//                        ]),
//                    ]]);
//            } else
            if ($stateVariable == OrderInterface::ORDER_SHIPMENT_STATE) {
                $form->add('value', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Choice([
//                            Shipment::STATE_NOT_SHIPPED,
//                            Shipment::STATE_PARTIALLY_SHIPPED,
//                            Shipment::STATE_SHIPPED,
//                            Shipment::STATE_DELIVERED,
                            Shipment::STATE_BACK_ORDER,
                        ]),
                    ]]);

            } elseif ($stateVariable == OrderInterface::ORDER_GENERAL_STATE) {
                $form->add('value', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Choice([
                            Order::STATE_FULFILLED,
                            Order::STATE_CANCELLED,
//                            Order::STATUS_PLACED,
                        ]),
                    ]]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'state_variable' => ""
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
