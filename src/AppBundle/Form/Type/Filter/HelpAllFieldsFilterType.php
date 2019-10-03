<?php

namespace AppBundle\Form\Type\Filter;


use AppBundle\Grid\Filter\HelpAllFieldsFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HelpHeaderFilterType
 * @package AppBundle\Form\Type\Filter
 */
class HelpAllFieldsFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($options['type'])) {
            $builder
                ->add('type', ChoiceType::class, [

                    'choices' => [
                        'sylius.ui.contains' => HelpAllFieldsFilter::TYPE_CONTAINS,
                        'sylius.ui.not_contains' => HelpAllFieldsFilter::TYPE_NOT_CONTAINS,
                    ],
                ]);
        }

        $builder
            ->add('value', TextType::class, [
                'required' => false,
                'label'=>'Search',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
            ->setDefined('type')
            ->setAllowedValues('type', [
                HelpAllFieldsFilter::TYPE_CONTAINS,
                HelpAllFieldsFilter::TYPE_NOT_CONTAINS,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_grid_filter_all';
    }
}