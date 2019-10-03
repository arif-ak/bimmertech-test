<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PageDescription;
use AppBundle\Entity\PageDescriptionImage;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageDescriptionType
 *
 * @package AppBundle\Form\Type
 */
class PageDescriptionType extends AbstractType
{

    /**
     * @var  ImageUploaderInterface
     */
    private $uploader;

    /**
     * PageDescriptionType constructor.
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header')
            ->add('subHeader')
            ->add('content', CKEditorType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'left' => 'left',
                    'right' => 'right',
                    'center' => 'center',
                ]
            ])
            ->add('video')
            ->add('images', CollectionType::class, [
                'entry_type' => PageDescriptionImageType::class,
                'button_add_label' => 'Add image',
                'button_delete_label' => 'Delete image',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true
            ])->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $descriptions = $event->getData()->getImages();

                /** @var PageDescriptionImage $image */
                foreach ($descriptions as $image) {

                    if ($image->hasFile()) {
                        $this->uploader->upload($image);
                        $image->getOwner() ?: $image->setOwner($descriptions->getOwner());
                    }
                    // Upload failed? Let's remove that image.
                    if (null === $image->getPath()) {
                        $descriptions->removeElement($image);
                    }
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageDescription::class,
        ]);
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'app_page_description_type';
    }
}
