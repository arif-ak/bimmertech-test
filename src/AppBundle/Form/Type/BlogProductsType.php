<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogProducts;
use AppBundle\Entity\Product;
use AppBundle\Entity\Taxon;
use AppBundle\Entity\TaxonProductRelated;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogProductsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'required' => true
                // 'empty_data'=>null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogProducts::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_blog_products_type';
    }
}
