<?php

namespace AppBundle\Form\Extension;

use AppBundle\Controller\TaxonomyController;
use AppBundle\Entity\PopupOption;
use AppBundle\Entity\Taxon;
use AppBundle\Form\Type\TaxonBestsellerType;
use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressTypeExtension
 * @package AppBundle\Form\Extension
 */
final class TaxonTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder->add('shortDescription', TextType::class, [
//            'required' => false,
//            'label' => 'Short Description',
//        ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Taxon $data */
            $data = $event->getData();
            $form = $event->getForm()
                ->remove('images')
                ->add('code', TextType::class, [
                    'label' => 'ID'
                ])
                ->add('seoText', CKEditorType::class, [
                    'required' => false,
                    'label' => 'SEO Text',
                ])
                ->add('metaTitle', TextType::class)
                ->add('metaKeywords', TextType::class)
                ->add('metaDescription', TextareaType::class)
                ->add('enabled');

            if (isset($data->type) || $data->getLevel() == Taxon::TAXON_CONTAINER || $data->isContainer()) {
                if ($data->type == TaxonomyController::CONTAINER ||
                    $data->getLevel() == Taxon::TAXON_CONTAINER || $data->isContainer()) {
                    $name = $data->getName();
                    $data->setProductName($name);
                    $form->add('shortDescription', CKEditorType::class, [
                        'required' => false,
                        'label' => 'Features',
                    ])
                        ->add('price', TextType::class)
                        ->add('bestseller', TaxonBestsellerType::class, [
                            'label' => false,
                        ])
                        ->add('popupOption', EntityType::class, [
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
                                return 'title: ' . $popupOption->getTitle() . ', code: ' . $popupOption->getCode();
                            },
                            'multiple' => true,
                            'expanded' => true
                        ])
                    ->add('teaser', TextareaType::class);
                }
            }

        })->addModelTransformer(new CallbackTransformer(
            function ($taxon) {
                /** @var Taxon $taxon */
                if ($taxon) {
                    $taxon->setPrice($taxon->getPrice() / 100);
                    return $taxon;
                }

            },
            function ($taxon) {
                /** @var Taxon $taxon */
                if ($taxon) {
                    $taxon->setPrice($taxon->getPrice() * 100);
                    return $taxon;
                }
            }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
        ));
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return TaxonType::class;
    }
}