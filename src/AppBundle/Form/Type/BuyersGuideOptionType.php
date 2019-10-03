<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BuyersGuideOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuyersGuideOptionType
 * @package AppBundle\Form\Type
 */
class BuyersGuideOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuyersGuideOption::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_buyers_guide_option_type';
    }
}
