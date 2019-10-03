<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostProduct;
use AppBundle\Entity\Product;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostProductsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('blogPost', EntityType::class, [
                'class' => BlogPost::class,
                'choice_label' => 'title'

            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'multiple' => true,
                'expanded'=> true,
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
            'data_class' => BlogPostProduct::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_blog_products_type';
    }
}
