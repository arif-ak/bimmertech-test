<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CustomerTypeExtension
 * @package AppBundle\Form\Extension
 */
class CustomerTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vatNumber');
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return CustomerType::class;
    }
}