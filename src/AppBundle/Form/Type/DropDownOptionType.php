<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\DropDownOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DropDownOptionType
 * @package AppBundle\Form\Type
 */
class DropDownOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('position')
            ->addModelTransformer(new CallbackTransformer(
                function ($dropDownOption) {
                    if ($dropDownOption) {
                        /** @var DropDownOption $dropDownOption */
                        if ($dropDownOption->getPrice()) {
                            $dropDownOption->setPrice($dropDownOption->getPrice() / 100);
                            return $dropDownOption;
                        }
                    }
                    return $dropDownOption;
                },
                function ($dropDownOption) {
                    if ($dropDownOption) {
                        /** @var DropDownOption $dropDownOption */
                        if ($dropDownOption->getPrice()) {
                            $dropDownOption->setPrice($dropDownOption->getPrice() * 100);
                            return $dropDownOption;
                        }
                    }
                    return $dropDownOption;
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DropDownOption::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_drop_down_option';
    }
}
