<?php

namespace AppBundle\Form\Extension;

use AppBundle\Entity\Color;
use AppBundle\Entity\DropDown;
use AppBundle\Entity\PopupOption;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductProperty;
use AppBundle\Form\Type\BuyersGuideImageType;
use AppBundle\Form\Type\BuyersGuideProductOptionType;
use AppBundle\Form\Type\BuyersGuideProductRelatedType;
use AppBundle\Form\Type\SavePriceType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AddressTypeExtension
 * @package AppBundle\Form\Extension
 */
final class ProductTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()
                ->remove('images')
                ->remove('description')
                ->remove('variantSelectionMethod')
                ->add('code', TextType::class, [
                    'label' => "SKU"
                ])
                ->add('taxonDescription', TextareaType::class, [
                    'label' => "Teaser"
                ])
                ->add('recomended',CheckboxType::class, [
                'label' => 'Recommended',
                ])
                ->add('properties', EntityType::class, [
                    'label' => false,
                    'class' => ProductProperty::class,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => function ($collection) {
                        /** @var EntityRepository $collection */
                        return
                            $collection->createQueryBuilder('p')
                                ->orderBy('p.name', 'ASC');
                    },
                    'choice_label' => function ($name) {
                        /** @var ProductProperty $name */
                        return $name->getName() . '; ID: ' . $name->getCode();
                    },
                ])
                ->add('buyersOption', CollectionType::class, [
                    'entry_type' => BuyersGuideProductOptionType::class,
                    'label' => false,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => 'Buyers guide Option'
                ])
                ->add('buyersImage', CollectionType::class, [
                    'entry_type' => BuyersGuideImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => false
                ])
                ->add('buyersRelated', CollectionType::class, [
                    'entry_type' => BuyersGuideProductRelatedType::class,
                    'label' => false,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true
                ])
                ->add('buyersHeaderContent', CKEditorType::class, [
                    'required' => false,
                    'label' => 'Buyers guide header content',
                ])
                ->add('buyersFooterContent', CKEditorType::class, [
                    'required' => false,
                    'label' => 'Buyers guide footer content',
                ])
                ->add('addonInformation', TextareaType::class, [
                    'label' => "Add-on pop-up"
                ])
                ->add('productDropDowns', EntityType::class, [
                    'class' => DropDown::class,
                    'label' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => function ($collection) {
                        /** @var EntityRepository $collection */
                        return
                            $collection->createQueryBuilder('d')
                                ->orderBy('d.name', 'ASC');
                    },
                    'choice_label' => function ($dropDowns) {
                        /** @var DropDown $dropDowns */
                        return $dropDowns->getName() . '; ID: ' . $dropDowns->getCode();
                    }

                ])
                ->add('productPopupOption', EntityType::class, [
                    'label' => false,
                    'class' => PopupOption::class,
                    'query_builder' => function ($collection) {
                        /** @var EntityRepository $collection */
                        return
                            $collection->createQueryBuilder('p')
                                ->orderBy('p.title', 'ASC');
                    },
                    'choice_label' => function ($popupOption) {
                        /** @var PopupOption $popupOption */
                        return $popupOption->getTitle() . '; ID: ' . $popupOption->getCode();
                    },
                    'multiple' => true,
                    'expanded' => true
                ])
                ->add('savePrice', SavePriceType::class, [
                    'label' => false
                ])
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Physical' => null,
                        'Coding' => 'codding',
                        'USB coding' => 'usb_coding'
                    ]
                ])
                ->add('description', TextareaType::class, [
                    'label' => false,
                    'attr' => [
                        'v-model' => 'productDescription',
                        'style' => 'display: none'
                    ]
                ])
                ->add('installer', TextareaType::class, [
                    'label' => false,
                    'attr' => [
                        'v-model' => 'productInstaller',
                        'style' => 'display: none'
                    ]
                ])
                ->add('interestingProducts', EntityType::class, [
                    'label' => false,
                    'class' => Product::class,
                    'query_builder' => function (EntityRepository $er) {
                        return
                            $er->createQueryBuilder('p')
                                ->innerJoin('p.translations', 't')
                                ->orderBy('t.name', 'ASC');
                    },
                    'choice_label' => function ($product) {
                        /** @var Product $category */
                        return $product->getName() . '; ID: ' . $product->getCode();
                    },
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('flagName')
                ->add('flagColor', EntityType::class, [
                    'required' => false,
                    'class' => Color::class,
                    'choice_label' => 'name',
                    'choice_value' => 'value',
                    'choice_attr' => function ($entry) {
                        return ['style ' => 'color:' . $entry];
                    },
                    'query_builder' => function ($collection) {
                        /** @var EntityRepository $collection */
                        return
                            $collection->createQueryBuilder('p')
                                ->orderBy('p.name', 'ASC');
                    }
                ]);
        });
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return ProductType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_product_property_type';
    }
}
