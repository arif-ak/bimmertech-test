<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Color;
use AppBundle\Entity\TaxonBestseller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TaxonBestsellerType
 * @package AppBundle\Form\Type
 */
class TaxonBestsellerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('color', EntityType::class, [
                'class' => Color::class,
                'choice_label' => 'name',
                'choice_value' => 'value',
                'choice_attr' => function ($value) {
                    return ['style ' => 'color:' . $value];
                }
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaxonBestseller::class,
        ]);
    }
}