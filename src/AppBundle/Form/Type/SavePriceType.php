<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\SavePrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SavePriceType
 * @package AppBundle\Form\Type
 */
class SavePriceType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price', NumberType::class, [
                'label' => 'Discount amount',
                'attr' => [
                    'min'=>0
                    ]
            ])
            ->addModelTransformer(new CallbackTransformer(
                function ($savePrice) {
                    /** @var SavePrice $savePrice */
                    if ($savePrice) {
                        $savePrice->setPrice($savePrice->getPrice() / 100);
                        return $savePrice;
                    }

                },
                function ($savePrice) {
                    /** @var SavePrice $savePrice */
                    if ($savePrice) {
                        $savePrice->setPrice($savePrice->getPrice() * 100);
                        return $savePrice;
                    }
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SavePrice::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_save_price';
    }
}
