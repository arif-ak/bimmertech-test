<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BuyersGuideOption;
use AppBundle\Entity\BuyersGuideProductOption;
use AppBundle\Service\ImageUploader;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuyersGuideOptionType
 * @package AppBundle\Form\Type
 */
class BuyersGuideProductOptionType extends ImageType
{
    protected $uploader;

    /**
     * ProductPropertiesType constructor.
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploader $uploader)
    {
        parent::__construct(BuyersGuideProductOption::class);
        $this->uploader = $uploader;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('option', EntityType::class, [
                'class' => BuyersGuideOption::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => [
                    'data-form-collection' => 'add'
                ]
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $file = $data->getFile();
                if ($file) {
                    $this->uploader->upload($data);
                }
                // Upload failed? Let's remove that image.
                if (null === $data->getPath()) {
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuyersGuideProductOption::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'buyers_guide_image';
    }
}
