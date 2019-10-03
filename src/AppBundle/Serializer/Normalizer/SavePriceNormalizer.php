<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Product;
use AppBundle\Entity\SavePrice;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SavePriceNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class SavePriceNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {

        if ($object instanceof Product) {
            $savePrice = $object->getSavePrice();
        } elseif ($object instanceof SavePrice) {
            $savePrice = $object;
        }

        if (isset($savePrice) && $savePrice) {
            return [
                'id' => $savePrice->getId(),
                'title' => $savePrice->getTitle(),
                'price' => $savePrice->getPrice(),
            ];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof SavePrice;
    }
}