<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BlogPostImage;
;

use AppBundle\Service\ImageUploader;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlogPostImageType
 * @package AppBundle\Form\Type
 */
class BlogPostImageType extends AbstractType
{
    protected $uploader;

    /**
     * @param ImageUploader $uploader
     */
    public function __construct(ImageUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aspectRatio', TextType::class, [
                'label' => 'Aspect ratio',
                'attr' => array('readonly' => 'readonly'),
            ])
            ->add('isUpdatedFile', HiddenType::class)
            ->add('file', FileType::class, [
                'label' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $file = $data->getFile();

                if ($data->getIsUpdatedFile() == "removed" || ($data->getPath() !== null && $file)) {
                    $this->uploader->remove($data->getPath());
                    $data->setPath(null);
                }

                if ($file) {
                    $this->uploader->upload($data);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPostImage::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'blog_post_image';
    }
}
