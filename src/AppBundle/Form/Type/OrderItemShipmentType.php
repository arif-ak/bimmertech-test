<?php

namespace AppBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class OrderItemTypeExtension
 * @package AppBundle\Form\Extension
 */
class OrderItemShipmentType extends AbstractResourceType
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * OrderItemWarehouseType constructor.
     *
     * @param string $dataClass
     * @param array $validationGroups
     * @param ObjectManager $objectManager
     */
    public function __construct(string $dataClass, $validationGroups = [], ObjectManager $objectManager)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->objectManager = $objectManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()
                ->add('tracking', TextType::class, [
                    'label' => 'Tracking number',
                    'required' => false
                ])
                ->add('dhlNumberOfPieces', IntegerType::class, [
                    'label' => 'Number of pieces for DHL',
                    'required' => false,
                    'attr' => [
                        'min' => 1
                    ]
                ])
                ->add('dhlWeight', TextType::class, [
                    'label' => 'Weight for DHL',
                    'required' => false
                ])
                ->add('dhlInsuredAmount', TextType::class, [
                    'label' => 'Insured Amount for DHL ',
                    'required' => false,
                    'attr' => ['placeholder' => 'Value with two decimals',],
                ])
                ->add('shipMethod', ChoiceType::class, [
                    'choices' => $event->getData()->getShipMethod(),
                    'choice_label' => 'name',
                ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_order_item_shipment';
    }
}