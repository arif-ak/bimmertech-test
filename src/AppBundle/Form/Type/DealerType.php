<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Dealer;
use AppBundle\Entity\DealerImage;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class DealerType extends AbstractType
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
            ->add('name')
            ->add('content')
            ->add('linkForButton')
            ->add('otherContent', CKEditorType::class)
            ->add('images', CollectionType::class, [
                'entry_type' => DealerImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => 'Dealer image',
            ])->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $dealer = $event->getData()->getImages();

                /** @var DealerImage $image */
                foreach ($dealer as $image) {
                    if ($image->hasFile()) {
                        $this->uploader->upload($image);
                        $image->getOwner() ?: $image->setOwner($image->getOwner());
                    }
                    // Upload failed? Let's remove that image.
                    if (null === $image->getPath()) {
                        $dealer->removeElement($image);
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
            'data_class' => Dealer::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_dealer_type';
    }
}
