<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogPostProduct;
use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlogPostProductType
 * @package AppBundle\Form\Type
 */
class BlogPostProductType extends AbstractType
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
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPostProduct::class,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'blog_post_product_type';
    }
}
