<?php

namespace AppBundle\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class AddressTypeExtension
 * @package AppBundle\Form\Extension
 */
final class ProductAttributeChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @param RepositoryInterface $attributeRepository
     */
    public function __construct(RepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {

                    return $this->attributeRepository->createQueryBuilder('p')
                        ->innerJoin('p.translations', 't')
                        ->orderBy('t.name', 'ASC')->getQuery()->getResult();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ]);
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return ProductAttributeChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'sylius_attribute_choice_ext';
    }
}
