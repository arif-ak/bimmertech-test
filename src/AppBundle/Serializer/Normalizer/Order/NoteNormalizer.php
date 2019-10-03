<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\OrderNote;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class NoteNormalizer
 */
class NoteNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $result = [];
        foreach ($object as $orderNote) {
            /** @var  OrderNote $orderNote */
            $result[] = [
                'id' => $orderNote->getId(),
                'date' => $orderNote->getCreatedAt()->format('m/d/y'),
                'author' => $orderNote->getAuthor(),
                'message' => $orderNote->getMessage()
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderNote;
    }
}
