<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\OrderItem;
use AppBundle\Entity\Product;
use AppBundle\VinCheck\CompatibilityProducts;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class OrderItemIncludedAddonsNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class OrderItemIncludedAddonsNormalizer implements NormalizerInterface
{
    /**
     * @var CompatibilityProducts
     */
    private $compatibilityService;

    /**
     * OrderNormalizer constructor.
     *
     * @param CompatibilityProducts $compatibilityService
     */
    public function __construct($compatibilityService)
    {
        $this->compatibilityService = $compatibilityService;
    }

    /**
     * @param Product $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var Collection $addons */
        $addons = $object->getAddons();
        $array = [];

        $includedAddons = $addons->filter(function ($addon) {
            if ($addon->getType() == 'includedAddon') {
                return $addon;
            }
        })->toArray();
        foreach ($includedAddons as $includedAddon) {
            $array[] = $this->prepareArray($includedAddon, $context);
        }
        return $array ?: [];
    }

    /**
     * Prepare response data
     *
     * @param $object
     * @param $context
     * @return array
     */
    private function prepareArray($object, $context)
    {
        /** @var Session $session */
        $session = isset($context['session']) ? $context['session'] : null;
        $product = $object->getProduct();
        /** @var OrderItem $object */
        $variant = $product->getVariants()->first();

        $data = [
            'variantId' => $variant->getId(),
            'name' => $product->getName(),
            'quantity'=>$object->getQuantity()
        ];

        if ($session) {
            $data['compatibility'] = $this->checkCompatibility($product, $session);
        }

        return $data;
    }

    /**
     * Check compatibility
     *
     * @param $product
     * @param $session
     * @return mixed|string
     */
    private function checkCompatibility($product, $session)
    {
        /** @var Session $session */
        if ($vinData = $session->get('vincheck')) {
            return $this->compatibilityService->checkCompatibilityProduct($product, $vinData);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}

