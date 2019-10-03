<?php

namespace AppBundle\Serializer\Normalizer\OrderItem;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class DropDownOptionNormalizer
 *
 * @package AppBundle\Serializer\Normalizer\OrderItem
 */
class DropDownOptionNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $dropDownOptions = $object->getDropDownOptions()->toArray();
        return array_map(function ($option) {
            /** @var DropDownOption $option */
            /** @var DropDown $dropDown */
            $dropDown = $option->getDropDown();
            return [
                'id' => $dropDown->getId(),
                'name' => $dropDown->getName(),
                'code' => $dropDown->getCode(),
                'selectedOption' => [
                    'id' => $option->getId(),
                    'name' => $option->getName(),
                    'price' => $option->getPrice()
                ]
            ];

        }, $dropDownOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DropDownOption;
    }
}