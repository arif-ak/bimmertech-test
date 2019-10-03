<?php

namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Webburza\Sylius\ArticleBundle\Form\Type\ArticleType;

/**
 * Class ArticleType
 * @package AppBundle\Form\Type
 */
final class AppArticleType extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('homepage')
            ->add('author')
            ->add('authorPhoto');
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return ArticleType::class;
    }

}