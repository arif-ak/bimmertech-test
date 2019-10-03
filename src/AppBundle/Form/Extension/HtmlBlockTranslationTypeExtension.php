<?php

namespace AppBundle\Form\Extension;

use BitBag\SyliusCmsPlugin\Form\Type\Translation\HtmlBlockTranslationType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class HtmlBlockTranslationTypeExtension
 * @package AppBundle\Form\Extension
 */
class HtmlBlockTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('content')
            ->add('content', CKEditorType::class, [
                'required' => false,
                'label' => 'bitbag_sylius_cms_plugin.ui.content',
            ]);
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return HtmlBlockTranslationType::class;
    }
}