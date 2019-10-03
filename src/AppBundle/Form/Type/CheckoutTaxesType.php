<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class CheckoutTaxesType
 * @package AppBundle\Form\Type
 */
class CheckoutTaxesType extends AbstractResourceType implements FormTypeInterface
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vatNumber', TextType::class, [
                'label' => "Vat number",
                'attr' => [
                    'onblur' => 'document.checkVatNumber.vatNumber.value = this.value;'
                ]
            ]);

    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_checkout_taxes_type';
    }


//    /**
//     * {@inheritdoc}
//     */
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//
//        $builder
//            ->add('vatNumber', TextType::class, [
//            'label' => "Vat number",
//        ])
//            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
//                $form = $event->getForm();
//                $resource = $event->getData();
//                $customer = $options['customer'];
//
//                Assert::isInstanceOf($resource, CustomerAwareInterface::class);
//                /** @var CustomerInterface $resourceCustomer */
//                $resourceCustomer = $resource->getCustomer();
//
//                if (
//                    (null === $customer && null === $resourceCustomer) ||
//                    (null !== $resourceCustomer && null === $resourceCustomer->getUser())
//                ) {
//                    $form->add('customer', CustomerGuestType::class, ['constraints' => [new Valid()]]);
//                }
//            });
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        parent::configureOptions($resolver);
//
//        $resolver
//            ->setDefaults([
//                'customer' => null,
//            ]);
//    }
//
//
}
