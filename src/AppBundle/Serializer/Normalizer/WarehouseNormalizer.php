<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\ShippingMethod;
use AppBundle\Entity\Warehouse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WarehouseNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Warehouse $object */
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'country' => $object->getCountry(),
            'city' => $object->getCity(),
            'address' => $object->getAddress(),
            'ship_methods' => $object->getShippingMethod()->count() > 0 ?
                $this->shipMethods($object->getShippingMethod()) : []
        ];
    }

    public function shipMethods($shipMethods)
    {
        $shipMethodsArray = [];

        /** @var ShippingMethod $shipMethod */
        foreach ($shipMethods as $shipMethod) {
            $shipMethodsArray[] = [
                'id' => $shipMethod->getId(),
                'name' => $shipMethod->getName(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Warehouse;
    }
}
