<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Taxon;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxonProductSortType extends AbstractType
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
                'choice_value' => 'code',
                'label' => false,
                'required' => false,
                'empty_data' => null,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Taxon filter",
                'attr' => ["class" => "ui submit button"],
            ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_taxon_related_type';
    }
}