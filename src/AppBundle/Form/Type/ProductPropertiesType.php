<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ProductProperty;
use AppBundle\Entity\ProductPropertyImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPropertiesType extends AbstractType
{
    protected $uploader;

    /**
     * ProductPropertiesType constructor.
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploaderInterface $uploader)
    {

        $this->uploader = $uploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'ID'
            ])
            ->add('name')
            ->add('description')
            ->add('images', CollectionType::class, [
                'entry_type' => ProductPropertyImageType::class,
                'button_add_label' => 'add image',
                'button_delete_label' => 'delete image',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true
            ])->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $properties = $event->getData()->getImages();

                /** @var ProductPropertyImage $image */
                foreach ($properties as $image) {

                    if ($image->hasFile()) {
                        $this->uploader->upload($image);
                        $image->getOwner() ?: $image->setOwner($properties->getOwner());
                    }
                    // Upload failed? Let's remove that image.
                    if (null === $image->getPath()) {
                        $properties->removeElement($image);
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
            'data_class' => ProductProperty::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_property';
    }
}
