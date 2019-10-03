<?php

namespace AppBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrderSortByStatusesFilterType
 * @package AppBundle\Form\Type\Filter
 */
class OrderSortByStatusesFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sortByStatus',ChoiceType::class,
            [
                'choices' => [
                    'My' => '0',
                    'Default' => '1',
                ],
            ]
        );
    }
}
