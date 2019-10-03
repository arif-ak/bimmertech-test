<?php

namespace AppBundle\Serializer\Normalizer\MediaFolder;

use AppBundle\Entity\MediaFolder;
use AppBundle\Entity\MediaFolderInterface;
use AppBundle\Entity\MediaImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MediaFolderNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class MediaFolderNormalizer implements NormalizerInterface
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

        return $this->getChildren($object);



//            $this->getData($object, true, $object->getParent(), $object->getImages(), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(MediaFolderInterface $mediaFolder, $hasChildren = false, $parent = null, $images = false, $context = [])
    {

        $data = [
            'id' => $mediaFolder->getId(),
            'name' => $mediaFolder->getName(),
            'parent' => $parent,
            'folderCount' => $mediaFolder->getFoldersCount(),
            'imagesCount' => $mediaFolder->getImagesCount(),
            'createdAt' => $mediaFolder->getCreatedAt()->format('d-m-Y H:i:s'),
            'type' => 'folder',
        ];

        if ($hasChildren) {
            $data['children'] = $this->getChildren($mediaFolder);
        }
        if ($parent) {
            $data['parent'] = $this->getData($mediaFolder->getParent());
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
