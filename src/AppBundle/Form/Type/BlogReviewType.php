<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogReview;
use AppBundle\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlogReviewType
 * @package AppBundle\Form\Type
 */
class BlogReviewType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class)
        ->add('comment', TextType::class)
        ->add('rating', IntegerType::class)
        ->add('status', ChoiceType::class, [
            'label' => 'Status',
            'choices' => [
                'Accepted' => BlogReview::STATUS_ACCEPTED,
                'New' => BlogReview::STATUS_NEW,
                'Rejected' => BlogReview::STATUS_REJECTED,
            ]
        ])
        ->add('author', EntityType::class, [
            'class' => Customer::class,
            'choice_label' => 'firstName',
            'required' => false,
        ])
        ->add('reviewSubject', EntityType::class, [
            'class' => BlogPost::class,
            'choice_label' => 'title',
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogReview::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'blog_review';
    }
}
