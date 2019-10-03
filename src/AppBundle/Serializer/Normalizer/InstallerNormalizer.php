<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Installer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductReviews
 * @package AppBundle\Serializer\Normalizer
 */
class InstallerNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return array_map(function ($installerObject) {
            /** @var  Installer $installerObject */
            return [
                'id' => $installerObject->getId(),
                'active' => false,
                'name' => $installerObject->getName(),
                'address' => $installerObject->getAddress(),
                'link' => $installerObject->getLink(),
                'type' => $installerObject->getType(),
                'longitude' => (float)$installerObject->getLongitude(),
                'latitude'  => (float)$installerObject->getLatitude()
            ];
        }, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Installer;
    }
}
