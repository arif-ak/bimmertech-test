<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Warehouse;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class OrderItemWarehouseType
 * @package AppBundle\Form\Type
 */
class OrderItemWarehouseType extends AbstractResourceType
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
                ->add('warehouse', EntityType::class, [
                    'class' => Warehouse::class
                ]);
        })
//            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
//                $event->getData()->setShipment($event->getData()->getWarehouse()->getShippingMethod()->first());
//            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_order_item_warehouse';
    }
}
