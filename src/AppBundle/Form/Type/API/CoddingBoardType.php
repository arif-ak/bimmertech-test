<?php

namespace AppBundle\Form\Type\API;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class CoddingBoardType extends AbstractType
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
            ])
            ->add('status', TextType::class, [
                'constraints' => [
                    new Choice([
                        OrderItemInterface::NOT_CODED,
                        OrderItemInterface::COMPLETE
                    ]),
                ],
            ]);
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
