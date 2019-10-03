<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Product;
use AppBundle\Entity\Taxon;
use AppBundle\Entity\TaxonProductRelated;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TaxonProductRelatedType
 * @package AppBundle\Form\Type
 */
class TaxonProductRelatedType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Taxon::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return
                        $er->createQueryBuilder('p')
                            ->innerJoin('p.translations', 't')
                            ->orderBy('t.name', 'ASC');
                },
                'choice_label' => function ($product) {
                    /** @var Product $product */
                    return $product->getName() . '; ID: ' . $product->getCode();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaxonProductRelated::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_taxon_related_type';
    }
}