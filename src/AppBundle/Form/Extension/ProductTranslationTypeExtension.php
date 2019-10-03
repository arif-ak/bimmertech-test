<?php
declare(strict_types=1);

namespace AppBundle\Form\Extension;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProductTranslationTypeExtension
 * @package AppBundle\Form\Extension
 */
final class ProductTranslationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('description')
            ->remove('shortDescription')
            ->add('shortDescription', CKEditorType::class, [
                'required' => false,
                'label' => 'Features',
            ]);
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return ProductTranslationType::class;
    }
}