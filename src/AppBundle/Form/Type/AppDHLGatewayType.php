<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AppDHLGatewayType
 * @package AppBundle\Form\Type
 */
final class AppDHLGatewayType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gatewayName', TextType::class, [
                'label' => 'gateway Name',
            ])
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => [
                    'Staging' => 'staging',
                    'Production' => 'production',
                ],
            ])
            ->add('dhlId', TextType::class, [
                'label' => 'DHL Id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'DHL Id cannot be blank.',
                        'groups' => 'bitbag',
                    ])
                ],
            ])
            ->add('pass', TextType::class, [
                'label' => 'bitbag.ui.dhl_password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Password cannot be blank.',
                        'groups' => 'bitbag',
                    ])
                ],
            ])
            ->add('shipperAccountNumber', TextType::class, [
                'label' => 'Shipper Account Number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Shipper Account Number cannot be blank.',
                        'groups' => 'bitbag',
                    ])
                ],
            ])
//            ->add('billingAccountNumber', TextType::class, [
//                'label' => 'Billing Account Number',
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Billing Account Number cannot be blank.',
//                        'groups' => 'bitbag',
//                    ])
//                ],
//            ])
//            ->add('dutyAccountNumber', TextType::class, [
//                'label' => 'Duty Account Number',
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Duty Account Number cannot be blank.',
//                        'groups' => 'bitbag',
//                    ])
//                ],
//            ])
//            ->add('shippingPaymentType', ChoiceType::class, [
//                'label' => 'bitbag.ui.shipping_payment_type',
//                'choices' => [
//                    'dhl.ui.shipper' => 'SHIPPER',
//                    'dhl.ui.receiver' => 'RECEIVER',
//                    'dhl.ui.user' => 'USER',
//                ],
//            ])
            ->add('regionCode', ChoiceType::class, [
                'label' => 'Region Code',
                'choices' => [
                    'AP' => 'AP',
                    'EU' => 'EU',
                    'AM' => 'AM',
                ],
            ])
            ->add('company', TextType::class, [
                'label' => 'Company name',
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'sylius.form.address.country',
                'enabled' => true
            ])
            ->add('name', TextType::class, [
                'label' => 'bitbag.ui.name',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone number',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'bitbag.ui.postal_code',
            ])
            ->add('city', TextType::class, [
                'label' => 'bitbag.ui.city',
            ])
            ->add('address', TextType::class, [
                'label' => 'Addresss',
            ])
//            ->add('dropOffType', ChoiceType::class, [
//                'label' => 'bitbag.ui.drop_off_type',
//                'choices' => [
//                    'bitbag.ui.request_courier' => 'REQUEST_COURIER',
//                    'bitbag.ui.courier_only' => 'COURIER_ONLY',
//                    'bitbag.ui.regular_pickup' => 'REGULAR_PICKUP',
//                ],
//            ])
//            ->add('serviceType', ChoiceType::class, [
//                'label' => 'bitbag.ui.service_type',
//                'choices' => [
//                    'bitbag.ui.AH' => 'AH',
//                    'bitbag.ui.domestic_09' => '09',
//                    'bitbag.ui.domestic_12' => '12',
//                    'bitbag.ui.EK' => 'EK',
//                    'bitbag.ui.PI' => 'PI',
//                ],
//            ])
//            ->add('labelType', ChoiceType::class, [
//                'label' => 'bitbag.ui.label_type',
//                'choices' => [
//                    'bitbag.ui.lp' => 'LP',
//                    'bitbag.ui.blp' => 'BLP',
//                    'bitbag.ui.lblp' => 'LBLP',
//                    'bitbag.ui.zblp' => 'ZBLP',
//                ],
//            ])
//            ->add('packageType', ChoiceType::class, [
//                'label' => 'bitbag.ui.type',
//                'choices' => [
//                    'bitbag.ui.package' => 'PACKAGE',
//                    'bitbag.ui.envelope' => 'ENVELOPE',
//                    'bitbag.ui.pallet' => 'PALLET',
//                ],
//            ])
//            ->add('shipmentStartHour', TextType::class, [
//                'label' => 'bitbag.ui.shipment_start_hour',
//            ])
//            ->add('shipmentEndHour', TextType::class, [
//                'label' => 'bitbag.ui.shipment_end_hour',
//            ])
//            ->add('pickupBreakingHour', TextType::class, [
//                'label' => 'bitbag.ui.pickup_breaking_hour',
//            ])
//            ->add('packageWidth', TextType::class, [
//                'label' => 'bitbag.ui.package_width',
//            ])
//            ->add('packageHeight', TextType::class, [
//                'label' => 'bitbag.ui.package_height',
//            ])
//            ->add('packageLength', TextType::class, [
//                'label' => 'bitbag.ui.package_length',
//            ])
        ;
    }
}