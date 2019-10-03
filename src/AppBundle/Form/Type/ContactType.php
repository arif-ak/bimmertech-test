<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactTitle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contactMainTitle', EntityType::class, [
                'class' => ContactTitle::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('contactPosition', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => [
                    'Horizontal' => Contact::HORIZONTAL_POSITION_CONTENT,
                    'Vertical' => Contact::VERTICAL_POSITION_CONTENT,
                ],
            ])
            ->add('contactContent', CollectionType::class, [
                'entry_type' => ContactContentType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_contact_type';
    }
}
