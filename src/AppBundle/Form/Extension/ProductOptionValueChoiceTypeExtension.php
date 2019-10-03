<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueChoiceType;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOptionValueChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    return $options['option']->getValues();
                },
                'choice_value' => 'code',
                'choice_label' => 'value',
                'choice_translation_domain' => false,
                'multiple' => false,
                'expanded' => true,
                'attr' => array('class' => 'variants')
            ])
            ->setRequired([
                'option',
            ])
            ->addAllowedTypes('option', [ProductOptionInterface::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ProductOptionValueChoiceType::class;
    }
}
