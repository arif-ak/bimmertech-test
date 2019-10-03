<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ChannelPricingTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('originalPrice', MoneyType::class, [
                'label' => 'sylius.ui.original_price',
                'currency' => $options['channel']->getBaseCurrency()->getCode(),
                'empty_data'=>'0.01'
            ])
            ->add('price', MoneyType::class, [
                'label' => false,
                'currency' => $options['channel']->getBaseCurrency()->getCode(),
                'empty_data'=>'0.01'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ChannelPricingType::class;
    }
}