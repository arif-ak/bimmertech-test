<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TrackingDeliveryType
 *
 * @package AppBundle\Form\Type
 */
final class TrackingDeliveryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('couriers', ChoiceType::class, [
                'label' => 'Couriers',
                'choices' => [
                    'DHL' => "dhl",
                    'USPS' => "usps",
                    'EMS' => "ems",
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Code cannot be blank.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Submit",
                'attr' => ["class" => "ui submit button"]
            ]);
    }
}
