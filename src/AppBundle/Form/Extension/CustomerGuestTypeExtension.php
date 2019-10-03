<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerGuestType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CustomerGuestTypeExtension
 * @package AppBundle\Form\Extension
 */
final class CustomerGuestTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('email')
            ->add('firstEmail', EmailType::class, [
                'label' => 'sylius.form.customer.email',
                'mapped' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'Confirm email',
            ]);

    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return CustomerGuestType::class;
    }
}