<?php

namespace AppBundle\Form\Extension;

use AppBundle\Entity\Warehouse;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Class ProductVariantTypeMyExtension
 * @package AppBundle\Form\Extension
 */
final  class ProductVariantTypeMyExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $productVariant = $event->getData();
            $event->getForm()
                ->add('defaultWarehouse', EntityType::class, [
                    'required'=>false,
                    'class' => Warehouse::class,
                    'choice_label' => 'name',
                    'label' => 'Default warehouse'
                ])
                ->add('imageRequired')
                ->add('instructionRequired')
                ->add('onHand', HiddenType::class)

                ->add('instruction')
//                ->add('installationTime')
                ->add('priority')
                ->add('vincheckserviceId' ,NumberType::class,[
                    'attr'=>[
                        'min'=>'0'
                    ]
                ])
//                ->add('hasHardware')
                ->add('hasSoftware', CheckboxType::class, [
                    "label" => "Requires coding"
                ]);
        });
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return ProductVariantType::class;
    }
}
