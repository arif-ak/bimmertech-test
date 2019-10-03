<?php

namespace AppBundle\Serializer\Normalizer\MediaFolder;

use AppBundle\Entity\MediaFolder;
use AppBundle\Entity\MediaFolderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class MediaFolderTreeNormalizer
 * @package AppBundle\Serializer\Normalizer\MediaFolder
 */
class MediaFolderTreeNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var MediaFolder $object */

        return $this->getData($object, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(MediaFolderInterface $mediaFolder, $hasChildren = false)
    {
        $data = [
            'id' => $mediaFolder->getId(),
            'name' => $mediaFolder->getName(),
            'folderCount' => $mediaFolder->getFoldersCount(),
            'imagesCount' => $mediaFolder->getImagesCount(),
            'createdAt' => $mediaFolder->getCreatedAt()->format('d-m-Y'),

        ];

        if ($hasChildren) {
            $data['children'] = $this->getChildren($mediaFolder, true);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(MediaFolderInterface $mediaFolder, $hasChildren = false)
    {
        $children = [];
        foreach ($mediaFolder->getChildren() as $item) {
            $children[] = $this->getData($item, $hasChildren);
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof MediaFolder;
    }

}
