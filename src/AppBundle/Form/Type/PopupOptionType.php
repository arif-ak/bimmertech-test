<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\FAQQuestion;
use AppBundle\Entity\PopupOption;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FAQQuestionType
 * @package AppBundle\Form\Type
 */
class PopupOptionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('vinCheckServiceId', ChoiceType::class, array(
                'label' => 'VinCheckService option',
                'choices' => array(
                    'Choose option from VinCheckService' => null,
                    'NBT' => PopupOption::NPT,
                    'PDC' => PopupOption::PDC,
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PopupOption::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_popup_option_type';
    }
}