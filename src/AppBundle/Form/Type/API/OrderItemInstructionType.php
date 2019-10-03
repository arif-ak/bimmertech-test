<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemInstructionType extends AbstractType
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
            ->add('order_items', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => OrderItem::class,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'allow_add' => true
            ])
            ->add('instruction', TextType::class)
            ->add('send_email', CheckboxType::class);
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
