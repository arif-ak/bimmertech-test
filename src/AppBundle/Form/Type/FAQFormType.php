<?php

namespace AppBundle\Form\Type;

use BitBag\SyliusCmsPlugin\Entity\FrequentlyAskedQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FAQFormType
 * @package AppBundle\Form\Type
 */
class FAQFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('code', TextType::class, [
                'label' => 'ID',
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Enabled',
            ])
            ->add('question', TextType::class, [
                'label' => 'Question',
            ])
            ->add('answer', TextareaType::class, [
                'label' => 'Aanswer',
            ])
            ->add('header', ChoiceType::class, array(
                'choices' => array(
                    'Installation & Tech Support' => 'installation',
                    'Payment & delivery' => 'payment',
                    'Returns and Refunds' => 'return',
                    'Your order' => 'order',
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FrequentlyAskedQuestion::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_faq_question_type';
    }
}
