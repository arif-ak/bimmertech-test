<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\PopupOption;
use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PopupOptionNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class PopupOptionNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {

        if (!$popupOptions = $object->getProductPopupOption()->toArray()) {
            return $popupOptions;
        }

        return array_map(function ($popupOption) {

            /** @var PopupOption $popupOption */
            return [
                'title' => $popupOption->getTitle(),
                'compatibility' => $popupOption->getCompatibility(),
                'vin_check_service_option_id' => $popupOption->getVinCheckServiceId()
            ];
        }, $popupOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}
