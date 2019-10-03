<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class InterestingProductsNormalizer
 *
 * @package AppBundle\Serializer\Normalizer
 */
class DropDownsNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {

        if (!$dropDowns = $object->getProductDropDowns()->toArray()) {
            return $dropDowns;
        }

        return array_map(function ($dropDown) {

            /** @var DropDown $dropDown */
            return [
                'id' => $dropDown->getId(),
                'name' => $dropDown->getName(),
                'code' => $dropDown->getCode(),
                'selectedOption' => $dropDown->getDropDownOptions() ? $dropDown->getDropDownOptions()->first()->getId() : null,
                'options' => $this->getOptions($dropDown->getDropDownOptions()),
            ];
        }, $dropDowns);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }

    /**
     * Get drop down option
     *
     * @param $options
     * @return array
     */
    private function getOptions($options)
    {
        $array = array_map(function ($option) {
            /** @var DropDownOption $option */
            return [
                'id' => $option->getId(),
                'name' => $option->getName(),
                'price' => $option->getPrice(),
                'position' => $option->getPosition()

            ];
        }, $options->toArray());

        return $array;
    }
}