<?php

namespace AppBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class HelpHeaderFilterType
 * @package AppBundle\Form\Type\Filter
 */
class HelpHeaderFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('header', ChoiceType::class, [
                'choices' =>[
                    ''=>'0',
                    'Installation & Tech Support' => '1',
                    'Payment & delivery' => '2',
                    'Returns and Refunds' => '3',
                    'Your order' => '4',
                ],
            ]
        );
    }
}