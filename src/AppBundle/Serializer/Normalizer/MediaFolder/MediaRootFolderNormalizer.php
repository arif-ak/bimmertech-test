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
class MediaRootFolderNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = [];
        foreach ($object as $item) {
            /** @var MediaFolder $item */
            $data[] = [
                'id' => $item->getId(),
                'parent' => null,
                'name' => $item->getName(),
                'folderCount' => $item->getFoldersCount(),
                'imagesCount' => $item->getImagesCount(),
                'createdAt' => $item->getCreatedAt()->format('d-m-Y H:i:s'),
                'type' => 'folder',
            ];
        }
        return $data;

    }

    /**
     * @param $images
     * @param null $format
     * @param $context
     * @return array
     */
    public function normalizeImage($images, $format = null, $context)
    {
        /** @var Request $request */
        $request = $context['request'];
        $data = [];
        /** @var MediaImage $image */
        foreach ($images as $image) {
            $path = $image->getPath();
            $data[] = [
                'id' => $image->getId(),
                'path' => $path ? '/' . $path : null,
                'icon' => $path ? '/media/cache/resolve/library_225_175/' . $path : null,
                'url' => $path ? $request->getSchemeAndHttpHost() . '/media/image/' . $image->getPath() : null,
                'name' => $image->getName(),
                'alt' => $image->getAlt(),
                'createdAt' => $image->getCreatedAt() ? $image->getCreatedAt()->format('d-m-Y H:i:s') : null,
                'folderId' => $image->getOwner() ? $image->getOwner()->getId() : null,
                'type' => 'image',
            ];
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
