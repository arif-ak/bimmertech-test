<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BuyersGuideProductRelated;
use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuyersGuideOptionType
 * @package AppBundle\Form\Type
 */
class BuyersGuideProductRelatedType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('relatedProduct', EntityType::class, [
                'label'=>false,
                'class' => Product::class,
                'choice_label' => 'name',
                'required' => false,
                'attr'=>[
                    'data-form-collection'=>'add'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuyersGuideProductRelated::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'sylius_buyers_guide_product_related_type';
    }
}
