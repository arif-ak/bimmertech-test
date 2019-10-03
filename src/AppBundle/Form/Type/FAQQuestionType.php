<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\FaqHeader;
use AppBundle\Entity\FAQQuestion;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FAQQuestionType
 * @package AppBundle\Form\Type
 */
class FAQQuestionType extends AbstractType
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
            ->add('answer', CKEditorType::class, [
                'label' => 'Answer',
            ])
            ->add('header', EntityType::class, [
                'class' => FaqHeader::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FAQQuestion::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_faq_question_type';
    }
}