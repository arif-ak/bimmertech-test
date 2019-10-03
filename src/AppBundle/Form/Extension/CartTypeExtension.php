<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\OrderBundle\Form\Type\CartType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CartTypeExtension
 * @package AppBundle\Form\Extension
 */
final class CartTypeExtension extends AbstractTypeExtension
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder->add('notes', TextareaType::class, [
//            'required' => false,
//            'label' => 'Comments',
//        ]);

    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return CartType::class;
    }
}