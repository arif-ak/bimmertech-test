<?php


namespace AppBundle\Form\Type;

use AppBundle\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrderShippingStatusType
 * @package AppBundle\Form\Type
 */
class OrderShippingStatusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shippingState', ChoiceType::class, [
            'label' => false,
            'choices' => [
                'Backorder' => 'backorder',
                'Partially shipped' => 'partially shipped',
                'Shipped' => 'shipped',
                'Delivered' => 'delivered'
            ]
        ])
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'save'),
            ));;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}